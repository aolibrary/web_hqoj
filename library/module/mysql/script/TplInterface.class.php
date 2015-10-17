<?php

require_once __DIR__ . '/../logic/TplLogic.class.php';

class TplInterface extends BaseInterface {

    /**
     * 获取满足条件的列表
     *
     * @param   array $params
     * @return  array
     * @throws  InterfaceException
     */
    public static function getList($params = array()) {
        $field  = self::get('field', $params, '*', TYPE_STR_Y);
        $where  = self::get('where', $params, array(), TYPE_ARR);
        $order  = self::get('order', $params, array(), TYPE_ARR);
        $limit  = self::get('limit', $params, -1, TYPE_INT);
        $offset = self::get('offset', $params, 0, TYPE_INT);
        return TplLogic::getList($field, $where, $order, $limit, $offset);
    }

    /**
     * 获取满足条件的记录数，支持group_by
     *
     * @param   array $params
     * @return  array|mixed
     */
    public static function getCount($params = array()) {
        $where  = $params;
        return TplLogic::getCount($where);
    }

    /**
     * 获取满足条件的一行
     *
     * @param   $params
     * @return  array
     * @throws  InterfaceException
     */
    public static function getRow($params) {
        $field  = self::get('field', $params, '*', TYPE_STR_Y);
        $where  = self::get('where', $params, array(), TYPE_ARR);
        $order  = self::get('order', $params, array(), TYPE_ARR);
        $offset = self::get('offset', $params, 0, TYPE_INT);
        return TplLogic::getRow($field, $where, $order, $offset);
    }

    /**
     * 给定一个或者一组id，返回满足条件的一行或者一个列表
     * 如果id是数组，那么返回的是一个以id为键值的hash
     *
     * @param   $params
     * @return  array
     * @throws  InterfaceException
     */
    public static function getById($params) {
        $id = self::get('id', $params, 0, TYPE_INT_GT0|TYPE_ARR, true);
        return TplLogic::getById($id);
    }

    /**
     * 给定一个或者一组value，获取满足条件的一行或者一个列表
     * 如果value是值，那么返回一行，如果value是数组，那么返回一个列表
     *
     * @param   $params array(
     *                      $field => $value
     *                  )
     * @return  array
     * @throws  InterfaceException
     */
    public static function getByField($params) {
        $field = key($params);
        self::check($field, TYPE_STR_Y);
        $value = self::get($field, $params, '', TYPE_NUM|TYPE_STR|TYPE_ARR, true);
        return TplLogic::getByField($field, $value);
    }

    /**
     * 通用添加和编辑接口，如果id不传，或者id=0，那么为插入新数据
     *
     * @param   array   $params 参数列表，通过judgeExist查看必传参数，通过initFromArray查看有效参数
     * @return  int     如果是新增，返回id；如果是更新，返回affected_rows
     * @throws  InterfaceException
     */
    public static function save($params = array()) {

        // 获取id，非必传
        $id = self::get('id', $params, 0, TYPE_INT);

        if (0 == $id) {

            // 必传参数
            self::judge($params, array(

            ));

            // 允许插入的字段
            $data = self::getAll($params, array(
                '{$fieldTypes}'
            ));

        } else {

            // 允许更新的字段
            $data = self::getAll($params, array(
                '{$fieldTypes}'
            ));

        }

        return TplLogic::save($data, $id);
    }

    /**
     * 通用删除接口，如果不是物理删除，记得修改Model中的其他接口，建议物理删除
     *
     * @param   array   $params 参数列表，通过getFromArray查看每个参数
     * @return  int     affected_rows
     * @throws  InterfaceException
     */
    public static function deleteById($params) {
        $id = self::get('id', $params, 0, TYPE_INT_GT0, true);
        return TplLogic::deleteById($id);
    }

}