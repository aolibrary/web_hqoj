<?php

require_once __DIR__ . '/../logic/DocLogic.class.php';

class DocInterface extends BaseInterface {

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
        return DocLogic::getList($field, $where, $order, $limit, $offset);
    }

    /**
     * 获取满足条件的记录数，支持group_by
     *
     * @param   array $params
     * @return  array|mixed
     */
    public static function getCount($params = array()) {
        $where  = $params;
        return DocLogic::getCount($where);
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
        return DocLogic::getRow($field, $where, $order, $offset);
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
        return DocLogic::getById($id);
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
        return DocLogic::getByField($field, $value);
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
                'user_id',
            ));

            // 允许插入的字段
            $data = self::getAll($params, array(
                'user_id'   => TYPE_INT_GT0,
            ));

        } else {

            // 允许更新的字段
            $data = self::getAll($params, array(
                'title'     => TYPE_STR_Y,
                'content'   => TYPE_STR,
            ));

        }

        return DocLogic::save($data, $id);
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
        return DocLogic::deleteById($id);
    }

    /**
     * 显示文章
     *
     * @param   $params
     * @return  int
     * @throws  InterfaceException
     */
    public static function show($params) {
        $id = self::get('id', $params, 0, TYPE_INT_GT0, true);
        return DocLogic::show($id);
    }

    /**
     * 隐藏文章
     *
     * @param   $params
     * @return  int
     * @throws  InterfaceException
     */
    public static function hide($params) {
        $id = self::get('id', $params, 0, TYPE_INT_GT0, true);
        return DocLogic::hide($id);
    }

    /**
     * 修改用户或者类别，也可以同时修改，或者只修改其中一个
     *
     * @param   $params
     * @return  int
     * @throws  InterfaceException
     */
    public static function change($params) {
        $id       = self::get('id', $params, 0, TYPE_INT_GT0, true);
        $username = self::get('username', $params, '', TYPE_STR_Y);
        $category = self::get('category', $params, -1, TYPE_INT);
        return DocLogic::change($id, $username, $category);
    }

}