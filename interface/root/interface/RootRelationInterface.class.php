<?php

require_once __DIR__ . '/../logic/RootRelationLogic.class.php';

class RootRelationInterface extends BaseInterface {

    public static function getList($params = array()) {
        $field  = self::get('field', $params, '*', TYPE_STR_Y);
        $where  = self::get('where', $params, array(), TYPE_ARR);
        $order  = self::get('order', $params, array(), TYPE_ARR);
        $limit  = self::get('limit', $params, -1, TYPE_INT);
        $offset = self::get('offset', $params, 0, TYPE_INT);
        return RootRelationLogic::getList($field, $where, $order, $limit, $offset);
    }

    public static function getCount($params = array()) {
        $where  = $params;
        return RootRelationLogic::getCount($where);
    }

    public static function getRow($params) {
        $field  = self::get('field', $params, '*', TYPE_STR_Y);
        $where  = self::get('where', $params, array(), TYPE_ARR);
        $order  = self::get('order', $params, array(), TYPE_ARR);
        $offset = self::get('offset', $params, 0, TYPE_INT);
        return RootRelationLogic::getRow($field, $where, $order, $offset);
    }

    public static function getById($params) {
        $id = self::get('id', $params, 0, TYPE_INT_GT0|TYPE_ARR, true);
        return RootRelationLogic::getById($id);
    }

    public static function getByField($params) {
        $field = key($params);
        self::check($field, TYPE_STR_Y);
        $value = self::get($field, $params, '', TYPE_NUM|TYPE_STR|TYPE_ARR, true);
        return RootRelationLogic::getByField($field, $value);
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

            // 必传参数校验
            self::judge($params, array(
                'manager_id',
                'path',
            ));

            // 插入数据过滤，以及参数
            $data = self::getAll($params, array(
                'manager_id'  => TYPE_INT_GT0,    // 管理员ID
                'path'        => TYPE_STR_Y,      // 路径
            ));

        } else {

            // 允许更新的字段，以及参数
            $data = self::getAll($params, array(

            ));

        }

        return RootRelationLogic::save($data, $id);
    }

    /**
     * 通用删除接口，如果不是物理删除，记得修改Model中的其他接口，建议物理删除
     *
     * @param   array   $params
     * @return  int     affected_rows
     * @throws  LibraryException
     */
    public static function deleteById($params) {
        $id = self::get('id', $params, 0, TYPE_INT_GT0, true);
        return RootRelationLogic::deleteById($id);
    }

    /**
     * 删除管理员的所有权限
     *
     * @param   $params
     * @return  int     affected_rows
     * @throws  LibraryException
     * @throws  InterfaceException
     */
    public static function deleteByManagerId($params) {
        $managerId = self::get('manager_id', $params, 0, TYPE_INT_GT0|TYPE_ARR_Y, true);
        $trans     = self::get('trans', $params, null, TYPE_OBJ);
        return RootRelationLogic::deleteByManagerId($managerId, $trans);
    }

    /**
     * 指定路径，删除关系
     *
     * @param   $params
     * @return  int     affected_rows
     * @throws  LibraryException
     * @throws  InterfaceException
     */
    public static function deleteByPath($params) {
        $path = self::get('path', $params, 0, TYPE_STR_Y, true);
        return RootRelationLogic::deleteByPath($path);
    }

    /**
     * 同步管理员的path到redis
     *
     * @param   $params
     * @throws  LibraryException
     */
    public static function syncToRedis($params) {
        $managerId = self::get('manager_id', $params, 0, TYPE_INT_GT0|TYPE_ARR_Y, true);
        RootRelationLogic::syncToRedis($managerId);
    }

    /**
     * 同步所有管理员的path到redis
     */
    public static function syncAllToRedis() {
        RootRelationLogic::syncAllToRedis();
    }

    /**
     * 获取不存在但是被使用的路径列表
     *
     * @return  array   array('name' => 路径名称, 'count' => 多少个管理员拥有)
     */
    public static function getInvalidPathList() {
        return RootRelationLogic::getInvalidPathList();
    }

}