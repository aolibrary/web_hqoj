<?php

require_once __DIR__ . '/../model/RootRelationModel.class.php';

class RootRelationLogic {

    public static function getList($field = '*', $where = array(), $order = array(), $limit = -1, $offset = 0) {
        $model = new RootRelationModel();
        return $model->getList($field, $where, $order, $limit, $offset);
    }

    public static function getCount($where = array()) {
        $model = new RootRelationModel();
        return $model->getCount($where);
    }

    public static function getRow($field = '*', $where = array(), $order = array(), $offset = 0) {
        $model = new RootRelationModel();
        return $model->getRow($field, $where, $order, $offset);
    }

    public static function getById($id) {
        $model = new RootRelationModel();
        return $model->getById($id);
    }

    public static function getByField($field, $value) {
        $model = new RootRelationModel();
        return $model->getByField($field, $value);
    }

    public static function save($data, $id = 0) {

        if (0 == $id) {

            $path       = $data['path'];
            $managerId  = $data['manager_id'];

            // 判断manager是否存在
            $managerInfo = RootManagerInterface::getById(array('id' => $managerId));
            if (empty($managerInfo)) {
                throw new InterfaceException('管理员不存在！');
            }

            // 判断路径是否存在
            if (!RootPermissionInterface::findPath(array('path' => $path))) {
                throw new InterfaceException('路径不存在！');
            }

            // 判断是否已经添加
            $check = RootManagerInterface::checkPermission(array(
                'id'    => $managerId,
                'path'  => $path,
            ));
            if ($check) {
                return 0;
            }

            $trans = new Trans(DbConfig::$SERVER_TRANS);
            $trans->begin();

            $model = new RootRelationModel($trans);

            // 删除重复权限
            $dir = rtrim($path, '/') . '/';
            $where = array(
                array('manager_id', '=', $managerId),
                array('path', 'LIKE', "{$dir}%"),
            );
            $model->delete($where);

            $insertData = $data;

            $id = $model->insert($insertData);
            $trans->commit();

            self::syncToRedis($managerId);

            return $id;
        } else {

            $model = new RootRelationModel();
            $updateData = $data;
            $affects = $model->updateById($id, $updateData);
            return $affects;
        }
    }

    public static function deleteById($id) {

        $rowInfo = self::getById($id);
        if (empty($rowInfo)) {
            return 0;
        }

        $model  = new RootRelationModel();
        $affects = $model->deleteById($id);
        if ($affects) {
            self::syncToRedis($rowInfo['manager_id']);
        }

        return $affects;
    }

    public static function deleteByManagerId($managerId, $trans = null) {

        if (empty($managerId)) {
            throw new InterfaceException('参数错误！');
        }

        if (!empty($trans)) {
            $model = new RootRelationModel($trans);
        } else {
            $model = new RootRelationModel();
        }

        $where = array();
        if (is_array($managerId)) {
            $where[] = array('manager_id', 'IN', $managerId);
        } else {
            $where[] = array('manager_id', '=', $managerId);
        }

        $affects = $model->delete($where);
        if ($affects) {
            $redis = RedisClient::getInstance(RedisConfig::$SERVER_COMMON);
            if (is_array($managerId)) {
                foreach ($managerId as $mId) {
                    $key = RedisKeys::ROOT_PATH_SET_ . $mId;
                    $redis->delete($key);
                }
            } else {
                $key = RedisKeys::ROOT_PATH_SET_ . $managerId;
                $redis->delete($key);
            }
        }
        return $affects;
    }

    public static function deleteByPath($path) {

        if (empty($path)) {
            throw new InterfaceException('参数错误！');
        }

        // 获取到受影响的manager
        $relationList = self::getByField('path', array($path));
        if (empty($relationList)) {
            return 0;
        }
        $ids = array_column($relationList, 'id');
        $where = array(
            array('id', 'IN', $ids),
        );
        $model = new RootRelationModel();
        $affects = $model->delete($where);
        $managerIds = array_unique(array_column($relationList, 'manager_id'));
        self::syncToRedis($managerIds);
        return $affects;
    }

    public static function syncToRedis($managerId) {

        $redis = RedisClient::getInstance(RedisConfig::$SERVER_COMMON);

        if (!is_array($managerId)) {
            $retList = self::getByField('manager_id', array($managerId));
            $pathSet = array_column($retList, 'path');
            sort($pathSet, SORT_STRING);
            $key = RedisKeys::ROOT_PATH_SET_ . $managerId;
            $redis->delete($key);
            foreach ($pathSet as $val) {
                $redis->sAdd($key, $val);
            }
        } else {
            $retList = self::getByField('manager_id', $managerId);
            $hash = array();
            foreach ($retList as $row) {
                $hash[$row['manager_id']][] = $row['path'];
            }
            // 对每个权限组进行排序
            foreach ($hash as $mId => $val) {
                sort($hash[$mId], SORT_STRING);
            }
            // 保存到redis
            foreach ($managerId as $mId) {
                $pathSet = Arr::get($mId, $hash, array());
                $key = RedisKeys::ROOT_PATH_SET_ . $mId;
                $redis->delete($key);
                foreach ($pathSet as $val) {
                    $redis->sAdd($key, $val);
                }
            }
        }
    }

    public static function syncAllToRedis() {

        $managerList = RootManagerInterface::getList(array(
            'field' => 'id',
        ));
        if (empty($managerList)) {
            return;
        }
        $managerIds = array_column($managerList, 'id');
        $retList = self::getList();
        $hash = array();
        foreach ($retList as $row) {
            $hash[$row['manager_id']][] = $row['path'];
        }

        // 对每个权限组进行排序
        foreach ($hash as $mId => $val) {
            sort($hash[$mId], SORT_STRING);
        }

        $redis = RedisClient::getInstance(RedisConfig::$SERVER_COMMON);

        $keys = $redis->keys(RedisKeys::ROOT_PATH_SET_ . '*');
        $redis->delete($keys);

        // 保存到redis
        foreach ($managerIds as $mId) {
            $pathSet = Arr::get($mId, $hash, array());
            $key = RedisKeys::ROOT_PATH_SET_ . $mId;
            foreach ($pathSet as $val) {
                $redis->sAdd($key, $val);
            }
        }
    }

    public static function getInvalidPathList() {

        $where = array(
            'group_by'  => 'path',
        );
        $order = array(
            'path'  => 'ASC',
        );
        $relationList = self::getList('path AS name, count(1) AS count', $where, $order);
        foreach ($relationList as $i => $info) {
            if (RootPermissionInterface::findPath(array('path' => $info['name']))) {
                unset($relationList[$i]);
            }
        }
        return $relationList;
    }

}