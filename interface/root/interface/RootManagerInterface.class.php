<?php

require_once __DIR__ . '/../logic/RootManagerLogic.class.php';

class RootManagerInterface extends BaseInterface {

    public static function getList($params = array()) {
        $field  = self::get('field', $params, '*', TYPE_STR_Y);
        $where  = self::get('where', $params, array(), TYPE_ARR);
        $order  = self::get('order', $params, array(), TYPE_ARR);
        $limit  = self::get('limit', $params, -1, TYPE_INT);
        $offset = self::get('offset', $params, 0, TYPE_INT);
        return RootManagerLogic::getList($field, $where, $order, $limit, $offset);
    }

    public static function getCount($params = array()) {
        $where  = $params;
        return RootManagerLogic::getCount($where);
    }

    public static function getRow($params) {
        $field  = self::get('field', $params, '*', TYPE_STR_Y);
        $where  = self::get('where', $params, array(), TYPE_ARR);
        $order  = self::get('order', $params, array(), TYPE_ARR);
        $offset = self::get('offset', $params, 0, TYPE_INT);
        return RootManagerLogic::getRow($field, $where, $order, $offset);
    }

    public static function getById($params) {
        $id     = self::get('id', $params, 0, TYPE_INT_GT0|TYPE_ARR, true);
        return RootManagerLogic::getById($id);
    }

    public static function getByField($params) {
        $field = key($params);
        self::check($field, TYPE_STR_Y);
        $value = self::get($field, $params, '', TYPE_NUM|TYPE_STR|TYPE_ARR, true);
        return RootManagerLogic::getByField($field, $value);
    }

    /**
     * 通用添加和编辑接口，如果id不传，或者id=0，那么为插入新数据
     *
     * @param   array   $params 参数列表，通过judgeExist查看必传参数，通过initFromArray查看有效参数
     * @return  int     如果是新增，返回id；如果是更新，返回affected_rows
     * @throws  LibraryException
     */
    public static function save($params = array()) {

        // 获取id，非必传
        $id = self::get('id', $params, 0, TYPE_INT);

        if (0 == $id) {

            // 插入数据必传参数校验
            self::judge($params, array(
                'login_name',
            ));

            // 插入数据过滤
            $data = self::getAll($params, array(
                'login_name'    => TYPE_STR_Y,    // 登陆名
            ));

        } else {

            // 允许更新的字段
            $data = self::getAll($params, array());

        }

        return RootManagerLogic::save($data, $id);
    }

    /**
     * 通用删除接口，如果不是物理删除，记得修改Model中的其他接口，建议物理删除
     *
     * @param   array   $params
     * @return  int     affected_rows
     * @throws  LibraryException
     */
    public static function deleteById($params) {
        $id     = self::get('id', $params, 0, TYPE_INT_GT0, true);
        return RootManagerLogic::deleteById($id);
    }

    /**
     * 启用一个用户，如果这个用户已经启用了，那么不操作，否则更新
     *
     * @param   $params
     */
    public static function enable($params) {
        $id = self::get('id', $params, 0, TYPE_INT_GT0, true);
        RootManagerLogic::enable($id);
    }

    /**
     * 禁用一个用户，如果这个用户已经禁用了，那么不操作，否则更新
     *
     * @param   $params
     */
    public static function forbid($params) {
        $id = self::get('id', $params, 0, TYPE_INT_GT0, true);
        RootManagerLogic::forbid($id);
    }

    /**
     * 获取某个user_id的有效管理员id
     *
     * @param   $params
     * @return  bool        如果非管理员或者管理员被禁用，那么返回0
     * @throws  LibraryException
     */
    public static function getEnabledId($params) {
        $userId    = self::get('user_id', $params, 0, TYPE_INT_GT0);
        $fromCache = self::get('from_cache', $params, false, TYPE_BOOL);
        return RootManagerLogic::getEnabledId($userId, $fromCache);
    }

    /**
     * 同步所有有效管理员到redis
     */
    public static function syncAllEnabledIdToRedis() {
        RootManagerLogic::syncAllEnabledIdToRedis();
    }

    /**
     * 判断用户是否拥有该权限，内部进行优化
     *
     * @param   $params
     * @return  bool
     * @throws  LibraryException
     */
    public static function checkPermission($params) {
        $id        = self::get('id', $params, 0, TYPE_INT_GT0);
        $path      = self::get('path', $params, '', TYPE_STR_Y);
        $fromCache = self::get('from_cache', $params, false, TYPE_BOOL);
        return RootManagerLogic::checkPermission($id, $path, $fromCache);
    }

    /**
     * 根据管理员获取权限组，'id'可以是数字也可以是数组，如果是数组，返回一组映射关系
     *
     * @param   $params
     * @return  array
     * @throws  LibraryException
     */
    public static function getPaths($params) {
        $id        = self::get('id', $params, 0, TYPE_INT_GT0|TYPE_ARR_Y, true);
        $fromCache = self::get('from_cache', $params, false, TYPE_BOOL);
        return RootManagerLogic::getPaths($id, $fromCache);
    }

    /**
     * 获取完全权限路径的所有管理者id
     *
     * @param   $params
     * @return  array       array( $id ... )
     * @throws  LibraryException
     */
    public static function getAllowedManagerIds($params) {
        $path = self::get('path', $params, '', TYPE_STR_Y);
        return RootManagerLogic::getAllowedManagerIds($path);
    }

    /**
     * 获取部分拥有权限路径的所有管理者id
     *
     * @param   $params
     * @return  array       array( $id ... )
     * @throws  LibraryException
     */
    public static function getIncludeManagerIds($params) {
        $path      = self::get('path', $params, '', TYPE_STR_Y);
        return RootManagerLogic::getIncludeManagerIds($path);
    }

}