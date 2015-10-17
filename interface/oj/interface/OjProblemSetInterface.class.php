<?php

require_once __DIR__ . '/../logic/OjProblemSetLogic.class.php';

class OjProblemSetInterface extends BaseInterface {

    public static function getList($params = array()) {
        $field  = self::get('field', $params, '*', TYPE_STR_Y);
        $where  = self::get('where', $params, array(), TYPE_ARR);
        $order  = self::get('order', $params, array(), TYPE_ARR);
        $limit  = self::get('limit', $params, -1, TYPE_INT);
        $offset = self::get('offset', $params, 0, TYPE_INT);
        return OjProblemSetLogic::getList($field, $where, $order, $limit, $offset);
    }

    public static function getCount($params = array()) {
        $where  = $params;
        return OjProblemSetLogic::getCount($where);
    }

    public static function getRow($params) {
        $field  = self::get('field', $params, '*', TYPE_STR_Y);
        $where  = self::get('where', $params, array(), TYPE_ARR);
        $order  = self::get('order', $params, array(), TYPE_ARR);
        $offset = self::get('offset', $params, 0, TYPE_INT);
        return OjProblemSetLogic::getRow($field, $where, $order, $offset);
    }

    public static function getById($params) {
        $id = self::get('id', $params, 0, TYPE_INT_GT0|TYPE_ARR, true);
        return OjProblemSetLogic::getById($id);
    }

    public static function getByField($params) {
        $field = key($params);
        self::check($field, TYPE_STR_Y);
        $value = self::get($field, $params, '', TYPE_NUM|TYPE_STR|TYPE_ARR, true);
        return OjProblemSetLogic::getByField($field, $value);
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

            // 必传参数
            self::judge($params, array(
                'user_id',
            ));

            // 允许插入的字段
            $data = self::getAll($params, array(
                'user_id'   => TYPE_INT_GT0,  // 专题创建人
            ));

        } else {

            // 允许更新的字段
            $data = self::getAll($params, array(
                'title'         => TYPE_STR_Y,
                'refresh_at'    => TYPE_INT,
            ));

        }

        return OjProblemSetLogic::save($data, $id);
    }

    /**
     * 通用删除接口，如果不是物理删除，记得修改Model中的其他接口，建议物理删除
     *
     * @param   array   $params 参数列表，通过getFromArray查看每个参数
     * @return  int     affected_rows
     * @throws  LibraryException
     */
    public static function deleteById($params) {
        $id = self::get('id', $params, 0, TYPE_INT_GT0, true);
        return OjProblemSetLogic::deleteById($id);
    }

    /**
     * 获取专题排行榜信息
     *
     * @param   $params
     * @return  array
     *          array(
     *              'rankHash'  array   user_id => rankInfo
     *              'mat'       array   ['user_id']['global_id'] => info
     *              'userHash'  array   user_id => userInfo
     *          )
     * @throws  InterfaceException
     * @throws  LibraryException
     */
    public static function getRankBoard($params) {
        $id = self::get('id', $params, 0, TYPE_INT_GT0, true);
        return OjProblemSetLogic::getRankBoard($id);
    }

    /**
     * 显示专题
     *
     * @param   $params
     * @return  int
     * @throws  InterfaceException
     * @throws  LibraryException
     */
    public static function show($params) {
        $id = self::get('id', $params, 0, TYPE_INT_GT0, true);
        return OjProblemSetLogic::show($id);
    }

    /**
     * 隐藏专题
     *
     * @param   $params
     * @return  int
     * @throws  InterfaceException
     * @throws  LibraryException
     */
    public static function hide($params) {
        $id = self::get('id', $params, 0, TYPE_INT_GT0, true);
        return OjProblemSetLogic::hide($id);
    }

    /**
     * 从专题中添加题目
     *
     * @param   $params
     * @return  int
     * @throws  InterfaceException
     * @throws  LibraryException
     */
    public static function addProblem($params) {
        $id          = self::get('id', $params, 0, TYPE_INT_GT0, true);
        $remote      = self::get('remote', $params, 0, TYPE_INT, true);
        $problemCode = self::get('problem_code', $params, '', TYPE_STR_Y, true);
        return OjProblemSetLogic::addProblem($id, $remote, $problemCode);
    }

    /**
     * 从专题中移除题目
     *
     * @param   $params
     * @return  int
     * @throws  InterfaceException
     * @throws  LibraryException
     */
    public static function removeProblem($params) {
        $id       = self::get('id', $params, 0, TYPE_INT_GT0, true);
        $globalId = self::get('global_id', $params, 0, TYPE_INT_GT0, true);
        return OjProblemSetLogic::removeProblem($id, $globalId);
    }

    /**
     * 置顶
     *
     * @param   $params
     * @return  int
     * @throws  InterfaceException
     * @throws  LibraryException
     */
    public static function stick($params) {
        $id = self::get('id', $params, 0, TYPE_INT_GT0, true);
        return OjProblemSetLogic::stick($id);
    }

    /**
     * 取消置顶
     *
     * @param   $params
     * @return  int
     * @throws  InterfaceException
     * @throws  LibraryException
     */
    public static function cancelStick($params) {
        $id = self::get('id', $params, 0, TYPE_INT_GT0, true);
        return OjProblemSetLogic::cancelStick($id);
    }

}