<?php

require_once __DIR__ . '/../model/RootManagerModel.class.php';

class RootManagerLogic {

    public static function getList($field = '*', $where = array(), $order = array(), $limit = -1, $offset = 0) {
        $model = new RootManagerModel();
        return $model->getList($field, $where, $order, $limit, $offset);
    }

    public static function getCount($where = array()) {
        $model = new RootManagerModel();
        return $model->getCount($where);
    }

    public static function getRow($field = '*', $where = array(), $order = array(), $offset = 0) {
        $model = new RootManagerModel();
        return $model->getRow($field, $where, $order, $offset);
    }

    public static function getById($id) {
        $model = new RootManagerModel();
        return $model->getById($id);
    }

    public static function getByField($field, $value) {
        $model = new RootManagerModel();
        return $model->getByField($field, $value);
    }

    public static function save($data, $id = 0) {

        $model = new RootManagerModel();
        if (0 == $id) {
            // 插入
            $userInfo = UserCommonInterface::getByLoginName(array(
                'login_name'    => $data['login_name'],
            ));
            if (empty($userInfo)) {
                throw new InterfaceException('用户不存在！');
            }

            // 不允许重复添加
            $managerInfo = self::getByField('user_id', $userInfo['id']);
            if (!empty($managerInfo)) {
                throw new InterfaceException("用户 {$data['login_name']} 已经是管理员！");
            }

            $insertData = array(
                'user_id'   => $userInfo['id'],
                'forbidden' => 0,
            );
            $id = $model->insert($insertData);

            // enable缓存
            $redis = RedisClient::getInstance(RedisConfig::$SERVER_COMMON);
            $key = RedisKeys::ROOT_ENABLED_HASH;
            $redis->hSet($key, $userInfo['id'], $id);

            return $id;
        } else {
            // 更新
            $updateData = $data;
            $affects = $model->updateById($id, $updateData);
            return $affects;
        }
    }

    public static function deleteById($id) {

        $managerInfo = self::getById($id);
        if (empty($managerInfo)) {
            return 0;
        }

        // 发起事务
        $trans = new Trans(DbConfig::$SERVER_TRANS);
        $trans->begin();

        $model = new RootManagerModel($trans);

        // 删除管理员权限 -> 删除管理员
        RootRelationInterface::deleteByManagerId(array(
            'manager_id' => $id,
            'trans' => $trans,
        ));
        $affects = $model->deleteById($id);

        // 删除redis中的数据
        $redis = RedisClient::getInstance(RedisConfig::$SERVER_COMMON);

        // 删除path
        $key = RedisKeys::ROOT_PATH_SET_ . $id;
        $redis->delete($key);

        // 删除user_id
        $key = RedisKeys::ROOT_ENABLED_HASH;
        $redis->hDel($key, $managerInfo['user_id']);

        $trans->commit();
        return $affects;
    }

    public static function enable($id) {

        $managerInfo = self::getById($id);
        if (empty($managerInfo) || !$managerInfo['forbidden']) {
            return;
        }
        $updateData = array(
            'forbidden' => 0,
        );
        $model = new RootManagerModel();
        $model->updateById($id, $updateData);

        // 同步缓存
        $redis = RedisClient::getInstance(RedisConfig::$SERVER_COMMON);
        $key = RedisKeys::ROOT_ENABLED_HASH;
        $redis->hSet($key, $managerInfo['user_id'], $id);
    }

    public static function forbid($id) {

        $managerInfo = self::getById($id);
        if (empty($managerInfo) || $managerInfo['forbidden']) {
            return;
        }
        $updateData = array(
            'forbidden' => 1,
        );
        $model = new RootManagerModel();
        $model->updateById($id, $updateData);

        // 同步缓存
        $redis = RedisClient::getInstance(RedisConfig::$SERVER_COMMON);
        $key = RedisKeys::ROOT_ENABLED_HASH;
        $redis->hDel($key, $managerInfo['user_id']);
    }

    public static function getEnabledId($userId, $fromCache = false) {

        if ($fromCache) {
            $redis = RedisClient::getInstance(RedisConfig::$SERVER_COMMON);
            $key = RedisKeys::ROOT_ENABLED_HASH;
            $id = $redis->hGet($key, $userId);
            return empty($id) ? 0 : $id;
        } else {
            self::syncEnabledIdToRedis($userId);
            return self::getEnabledId($userId, true);
        }
    }

    private static function syncEnabledIdToRedis($userId) {

        $redis = RedisClient::getInstance(RedisConfig::$SERVER_COMMON);
        $key = RedisKeys::ROOT_ENABLED_HASH;

        $managerInfo = self::getByField('user_id', $userId);
        if (empty($managerInfo) || $managerInfo['forbidden']) {
            $redis->hDel($key, $userId);
            return;
        }

        $redis->hSet($key, $userId, $managerInfo['id']);
    }

    public static function syncAllEnabledIdToRedis() {

        $where = array(
            array('forbidden', '=', 0),
        );
        $managerList = self::getList('id, user_id', $where);

        $redis = RedisClient::getInstance(RedisConfig::$SERVER_COMMON);
        $key = RedisKeys::ROOT_ENABLED_HASH;
        $redis->delete($key);

        foreach ($managerList as $managerInfo) {
            $redis->hSet($key, $managerInfo['user_id'], $managerInfo['id']);
        }
    }

    // 校验权限，path可能是权限码，可能是文件夹
    public static function checkPermission($id, $path, $fromCache = false) {

        if (!RootPermissionInterface::isValidPath(array('path' => $path))) {
            throw new InterfaceException('路径格式不合法！');
        }

        // 对path处理
        $path = rtrim($path, '/') . '/';

        $pathList = self::getPaths($id, $fromCache);
        foreach ($pathList as $val) {

            // 对var处理
            $val = rtrim($val, '/') . '/';
            if (0 === strpos($path, $val)) {
                return true;
            }
        }
        return false;
    }

    public static function getPaths($id, $fromCache = false) {

        if ($fromCache) {

            $redis = RedisClient::getInstance(RedisConfig::$SERVER_COMMON);

            if (!is_array($id)) {
                $key = RedisKeys::ROOT_PATH_SET_ . $id;
                $set = $redis->sMembers($key);
                return empty($set) ? array() : $set;
            } else {
                $retHash = array();
                foreach ($id as $mId) {
                    $key = RedisKeys::ROOT_PATH_SET_ . $mId;
                    $set = $redis->sMembers($key);
                    $retHash[$mId] = empty($set) ? array() : $set;
                }
                return $retHash;
            }
        } else {

            RootRelationInterface::syncToRedis(array('manager_id' => $id));
            return self::getPaths($id, true);
        }
    }

    // 获取完全拥有改权限的manager，返回一组id
    public static function getAllowedManagerIds($path) {

        // 对path处理
        $path = rtrim($path, '/') . '/';

        $relationList = RootRelationInterface::getList();
        foreach ($relationList as $i => $rowInfo) {
            $val = rtrim($rowInfo['path'], '/') . '/';
            if (0 === strpos($path, $val)) {
                continue;
            }
            unset($relationList[$i]);
        }
        return array_column($relationList, 'manager_id');
    }

    // 获取部分拥有改权限的manager，返回一组id
    public static function getIncludeManagerIds($path) {

        // 对path处理
        $path = rtrim($path, '/') . '/';

        $relationList = RootRelationInterface::getList();
        foreach ($relationList as $i => $rowInfo) {
            $val = rtrim($rowInfo['path'], '/') . '/';
            if (0 === strpos($val, $path)) {
                continue;
            }
            unset($relationList[$i]);
        }
        return array_column($relationList, 'manager_id');
    }

}