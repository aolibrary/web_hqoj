<?php

require_once __DIR__ . '/../model/UcUserModel.class.php';

class UcUserLogic {

    public static function getList($field = '*', $where = array(), $order = array(), $limit = -1, $offset = 0) {
        $model = new UcUserModel();
        return $model->getList($field, $where, $order, $limit, $offset);
    }

    public static function getCount($where = array()) {
        $model = new UcUserModel();
        return $model->getCount($where);
    }

    public static function getRow($field = '*', $where = array(), $order = array(), $offset = 0) {
        $model = new UcUserModel();
        return $model->getRow($field, $where, $order, $offset);
    }

    public static function getById($id) {
        $model = new UcUserModel();
        return $model->getById($id);
    }

    public static function getByField($field, $value) {
        $model = new UcUserModel();
        return $model->getByField($field, $value);
    }

    public static function save($data, $id = 0, $trans = null) {

        if (!empty($trans)) {
            $model = new UcUserModel($trans);
        } else {
            $model = new UcUserModel();
        }
        if (0 == $id) {
            // 插入
            $insertData = $data;

            // 密码加密
            $insertData['password'] = self::encryptPassword($insertData['password']);

            // 默认昵称
            $nickname = Arr::get('nickname', $insertData, '');
            if (empty($nickname)) {
                $insertData['nickname'] = $insertData['username'];
            }

            $id = $model->insert($insertData);
            return $id;
        } else {
            // 更新
            $updateData = $data;

            // 获取原来的用户
            $userInfo = self::getById($id);
            if (empty($userInfo)) {
                return 0;
            }

            // 密码加密
            if (array_key_exists('password', $updateData)) {
                $updateData['password'] = self::encryptPassword($updateData['password']);
            }

            // 如果修改邮箱
            if (!empty($updateData['email']) && $userInfo['email'] != $updateData['email']) {
                $tmpUser = self::getByLoginName($updateData['email']);
                if (!empty($tmpUser)) {
                    throw new InterfaceException('一个邮箱只能绑定一个用户！');
                }
            }

            $affects = $model->updateById($id, $updateData);
            return $affects;
        }
    }

    public static function deleteById($id) {
        $model  = new UcUserModel();
        return $model->deleteById($id);
    }

    public static function login($loginName, $password) {

        // 获取用户信息
        $userInfo = self::getByLoginName($loginName);

        if (empty($userInfo)) {
            return array( 'ret' => false, 'msg' => '用户不存在！');
        }

        if ($password != $userInfo['password']) {
            return array( 'ret' => false, 'msg' => '密码错误！');
        }

        $ticket = self::makeTicket();
        $key    = self::makeKey($userInfo['id'], $ticket);

        // 保存到memcached
        $cache = MemcachedPool::getMemcached(MemcachedConfig::$SERVER_COMMON);
        if (false === $cache->set($key, true, time()+3600)) {
            return array( 'ret' => false, 'msg' => '登陆系统繁忙，请稍后在试！');
        }

        // 写入cookie
        Cookie::set('cookie01', $userInfo['id']);
        Cookie::set('cookie02', $ticket);

        return array( 'ret' => true, 'user_info' => $userInfo);
    }

    // ticket只是防止登陆被破解，不保证唯一性
    private static function makeTicket() {
        $ticket = md5(Http::getClientIp() . Time::ms() . uniqid());
        return $ticket;
    }

    // 用来标识登陆状态, 具有唯一性
    private static function makeKey($id, $ticket) {
        return 'login_' . $id  . '_' . $ticket;
    }

    public static function logout() {

        $id     = Cookie::get('cookie01');
        $ticket = Cookie::get('cookie02');

        $cache = MemcachedPool::getMemcached(MemcachedConfig::$SERVER_COMMON);
        $key = self::makeKey($id, $ticket);
        $cache->delete($key);

        Cookie::delete('cookie01');
        Cookie::delete('cookie02');
    }

    public static function encryptPassword($password) {
        return sha1(md5($password));
    }

    public static function getLoginUserInfo() {

        $id     = Cookie::get('cookie01');
        $ticket = Cookie::get('cookie02');

        if (empty($id) || empty($ticket)) {
            return array();
        }

        $cache = MemcachedPool::getMemcached(MemcachedConfig::$SERVER_COMMON);
        $key = self::makeKey($id, $ticket);
        $isLogin = $cache->get($key);
        if (! $isLogin) {
            return array();
        }

        // 重新刷新缓存时间
        $cache->set($key, true, time()+3600);

        $userInfo = self::getById($id);
        return $userInfo;
    }

    public static function getByLoginName($loginName) {

        // 获取用户信息
        if (is_numeric($loginName[0])) {
            $userInfo = self::getByField('telephone', $loginName);
        } else if (strpos($loginName, '@')) {
            $userInfo = self::getByField('email', $loginName);
        } else {
            $userInfo = self::getByField('username', $loginName);
        }
        return $userInfo;
    }

}