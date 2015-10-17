<?php

require_once __DIR__ . '/../logic/RootPermissionLogic.class.php';

class RootPermissionInterface extends BaseInterface {

    public static function getList($params = array()) {
        $field  = self::get('field', $params, '*', TYPE_STR_Y);
        $where  = self::get('where', $params, array(), TYPE_ARR);
        $order  = self::get('order', $params, array(), TYPE_ARR);
        $limit  = self::get('limit', $params, -1, TYPE_INT);
        $offset = self::get('offset', $params, 0, TYPE_INT);
        return RootPermissionLogic::getList($field, $where, $order, $limit, $offset);
    }

    public static function getCount($params = array()) {
        $where  = $params;
        return RootPermissionLogic::getCount($where);
    }

    public static function getRow($params) {
        $field  = self::get('field', $params, '*', TYPE_STR_Y);
        $where  = self::get('where', $params, array(), TYPE_ARR);
        $order  = self::get('order', $params, array(), TYPE_ARR);
        $offset = self::get('offset', $params, 0, TYPE_INT);
        return RootPermissionLogic::getRow($field, $where, $order, $offset);
    }

    public static function getById($params) {
        $id = self::get('id', $params, 0, TYPE_INT_GT0|TYPE_ARR, true);
        return RootPermissionLogic::getById($id);
    }

    public static function getByField($params) {
        $field = key($params);
        self::check($field, TYPE_STR_Y);
        $value = self::get($field, $params, '', TYPE_NUM|TYPE_STR|TYPE_ARR, true);
        return RootPermissionLogic::getByField($field, $value);
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
                'code',
                'description',
            ));

            // 插入数据过滤，以及参数
            $data = self::getAll($params, array(
                'code'        => TYPE_STR_Y, // 权限码
                'description' => TYPE_STR_Y, // 权限名称
            ));

        } else {

            // 允许更新的字段，以及参数
            $data = self::getAll($params, array(
                'description' => TYPE_STR_Y, // 权限名称
            ));

        }

        return RootPermissionLogic::save($data, $id);
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
        return RootPermissionLogic::deleteById($id);
    }

    /**
     * 批量删除
     *
     * @param   array   $params
     * @return  int     affected_rows
     * @throws  LibraryException
     */
    public static function deleteMultiByIds($params) {
        $ids = self::get('ids', $params, array(), TYPE_ARR_Y, true);
        return RootPermissionLogic::deleteMultiByIds($ids);
    }

    /**
     * 返回适配jstree的json数据
     *
     * @param   $params
     * @return  string
     */
    public static function getPermissionTreeJson($params = array()) {
        $fromCache = self::get('from_cache', $params, false, TYPE_BOOL);
        return RootPermissionLogic::getPermissionTreeJson($fromCache);
    }

    /**
     * 权限码是否合法
     *
     * @param   $params
     * @return  bool
     * @throws  LibraryException
     */
    public static function isValidCode($params) {
        $code = self::get('code', $params, '', TYPE_STR_Y, true);
        return RootPermissionLogic::isValidCode($code);
    }

    /**
     * 路径名是否合法
     *
     * @param   $params
     * @return  bool
     * @throws  LibraryException
     */
    public static function isValidPath($params) {
        $path = self::get('path', $params, '', TYPE_STR_Y, true);
        return RootPermissionLogic::isValidPath($path);
    }

    /**
     * 测试权限是否可以被创建
     *
     * @param   $params
     * @return  bool    如果可以创建，返回true，否则返回false
     * @throws  LibraryException
     * @throws  InterfaceException
     */
    public static function testMakeCode($params) {
        $code = self::get('code', $params, '', TYPE_STR_Y, true);
        return RootPermissionLogic::testMakeCode($code);
    }

    /**
     * 查找路径，路径就是权限或者文件夹
     *
     * @param   $params
     * @return  null|false|array    如果找到，返回节点array
     *                              如果找到，但是和$path类型不匹配（权限码|文件夹），返回false
     *                              如果没有找到，返回null
     * @throws  LibraryException
     * @throws  InterfaceException
     */
    public static function findPath($params) {
        $path      = self::get('path', $params, '', TYPE_STR_Y, true);
        $fromCache = self::get('from_cache', $params, false, TYPE_BOOL);
        return RootPermissionLogic::findPath($path, $fromCache);
    }

}