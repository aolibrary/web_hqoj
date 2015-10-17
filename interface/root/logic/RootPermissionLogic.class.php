<?php

require_once INCLUDE_PATH . '/root/PermissionTree.class.php';
require_once __DIR__ . '/../model/RootPermissionModel.class.php';

class RootPermissionLogic {

    public static function getList($field = '*', $where = array(), $order = array(), $limit = -1, $offset = 0) {
        $model = new RootPermissionModel();
        return $model->getList($field, $where, $order, $limit, $offset);
    }

    public static function getCount($where = array()) {
        $model = new RootPermissionModel();
        return $model->getCount($where);
    }

    public static function getRow($field = '*', $where = array(), $order = array(), $offset = 0) {
        $model = new RootPermissionModel();
        return $model->getRow($field, $where, $order, $offset);
    }

    public static function getById($id) {
        $model = new RootPermissionModel();
        return $model->getById($id);
    }

    public static function getByField($field, $value) {
        $model = new RootPermissionModel();
        return $model->getByField($field, $value);
    }

    public static function save($data, $id = 0) {

        $model = new RootPermissionModel();
        if (0 == $id) {

            if (!self::isValidCode($data['code'])) {
                throw new InterfaceException('权限码不合法！');
            }

            if (!self::testMakeCode($data['code'])) {
                throw new InterfaceException('无法创建权限！');
            }

            $insertData = $data;

            $id = $model->insert($insertData);
            self::syncPermissionTree();
            return $id;
        } else {

            $updateData = $data;

            $affects = $model->updateById($id, $updateData);
            if ($affects) {
                self::syncPermissionTree();
            }
            return $affects;
        }
    }

    public static function deleteById($id) {
        $model  = new RootPermissionModel();
        $affects = $model->deleteById($id);
        if ($affects) {
            self::syncPermissionTree();
        }
        return $affects;
    }

    public static function deleteMultiByIds($ids) {
        $model = new RootPermissionModel();
        $where = array(
            array('id', 'IN', $ids),
        );
        $affects = $model->delete($where);
        if ($affects) {
            self::syncPermissionTree();
        }
        return $affects;
    }

    public static function getPermissionTree($fromCache = false) {

        if ($fromCache) {
            $memcached = MemcachedPool::getMemcached(MemcachedConfig::$SERVER_COMMON);
            $key = MemcachedKeys::ROOT_TREE;
            $tree = $memcached->get($key);
            if (false === $tree) {
                return self::getPermissionTree();
            }
            return $tree;
        } else {
            self::syncPermissionTree();
            return self::getPermissionTree(true);
        }
    }

    private static function syncPermissionTree() {

        $memcached = MemcachedPool::getMemcached(MemcachedConfig::$SERVER_COMMON);
        $key = MemcachedKeys::ROOT_TREE;

        $tree = new PermissionTree();   // 只有根的权限树

        $permissionList = self::getList();
        if (empty($permissionList)) {
            $memcached->set($key, $tree);
            return;
        }

        foreach ($permissionList as $permissionInfo) {
            $tree->insert($permissionInfo['id'], $permissionInfo['code'], $permissionInfo['description']);
        }
        $memcached->set($key, $tree);
    }

    public static function getPermissionTreeJson($fromCache) {
        $tree = self::getPermissionTree($fromCache);
        return $tree->getJsTreeJson();
    }

    // 校验权限码的合法性
    public static function isValidCode($code) {
        return PermissionTree::isValidCode($code);
    }

    public static function isValidPath($path) {
        return PermissionTree::isValidPath($path);
    }

    // 测试权限是否可以被创建
    public static function testMakeCode($code) {

        if (!self::isValidCode($code)) {
            throw new InterfaceException('权限码不合法！');
        }

        $tree = self::getPermissionTree();

        $arr = explode('/', trim($code, '/'));

        $path = '';
        $p = array();
        foreach ($arr as $next) {
            $path = $path . '/' . $next;
            $p = $tree->find($path);
            if (!$p) {
                return true;
            }
            if ($p['data'] == 'code') {
                return false;
            }
        }

        // 如果最后一个节点是文件夹，不能生成权限码
        if ($p['data'] == 'folder') {
            return false;
        }
        return true;
    }

    // 查找路径
    public static function findPath($path, $fromCache) {

        if (!self::isValidPath($path)) {
            throw new InterfaceException('权限路径不合法！');
        }
        $tree = self::getPermissionTree($fromCache);
        return $tree->find($path);
    }

}