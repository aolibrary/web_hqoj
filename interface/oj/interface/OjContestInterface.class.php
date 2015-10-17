<?php

require_once __DIR__ . '/../logic/OjContestLogic.class.php';

class OjContestInterface extends BaseInterface {

    public static function getList($params = array()) {
        $field  = self::get('field', $params, '*', TYPE_STR_Y);
        $where  = self::get('where', $params, array(), TYPE_ARR);
        $order  = self::get('order', $params, array(), TYPE_ARR);
        $limit  = self::get('limit', $params, -1, TYPE_INT);
        $offset = self::get('offset', $params, 0, TYPE_INT);
        return OjContestLogic::getList($field, $where, $order, $limit, $offset);
    }

    public static function getCount($params = array()) {
        $where  = $params;
        return OjContestLogic::getCount($where);
    }

    public static function getRow($params) {
        $field  = self::get('field', $params, '*', TYPE_STR_Y);
        $where  = self::get('where', $params, array(), TYPE_ARR);
        $order  = self::get('order', $params, array(), TYPE_ARR);
        $offset = self::get('offset', $params, 0, TYPE_INT);
        return OjContestLogic::getRow($field, $where, $order, $offset);
    }

    public static function getById($params) {
        $id = self::get('id', $params, 0, TYPE_INT_GT0|TYPE_ARR, true);
        return OjContestLogic::getById($id);
    }

    public static function getByField($params) {
        $field = key($params);
        self::check($field, TYPE_STR_Y);
        $value = self::get($field, $params, '', TYPE_NUM|TYPE_STR|TYPE_ARR, true);
        return OjContestLogic::getByField($field, $value);
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
        $trans = self::get('trans', $params, null, TYPE_OBJ);

        if (0 == $id) {

            // 必传参数
            self::judge($params, array(
                'is_diy',
                'user_id',
            ));

            // 允许插入的字段
            $data = self::getAll($params, array(
                'is_diy'    => TYPE_INT,
                'user_id'   => TYPE_INT_GT0,
            ));

        } else {

            // 允许更新的字段
            $data = self::getAll($params, array(
                'title'          => TYPE_STR_Y,
                'type'           => TYPE_INT,
                'password'       => TYPE_STR,
                'notice'         => TYPE_STR,
                'begin_time'     => TYPE_INT,
                'end_time'       => TYPE_INT,
                'description'    => TYPE_STR,
                'problem_hidden' => TYPE_INT,
                'is_active'      => TYPE_INT,
            ));

        }

        return OjContestLogic::save($data, $id, $trans);
    }

    /**
     * 通用删除接口，如果不是物理删除，记得修改Model中的其他接口，建议物理删除
     * @param   array   $params 参数列表，通过getFromArray查看每个参数
     * @return  int     affected_rows
     * @throws  LibraryException
     */
    public static function deleteById($params) {
        $id = self::get('id', $params, 0, TYPE_INT_GT0, true);
        return OjContestLogic::deleteById($id);
    }

    /**
     * 显示竞赛
     *
     * @param   $params
     * @return  int
     * @throws  InterfaceException
     * @throws  LibraryException
     */
    public static function show($params) {
        $id = self::get('id', $params, 0, TYPE_INT_GT0, true);
        return OjContestLogic::show($id);
    }

    /**
     * 隐藏竞赛
     *
     * @param   $params
     * @return  int
     * @throws  InterfaceException
     * @throws  LibraryException
     */
    public static function hide($params) {
        $id = self::get('id', $params, 0, TYPE_INT_GT0, true);
        return OjContestLogic::hide($id);
    }

    /**
     * 获取竞赛详情
     *
     * @param   $params
     * @return  array       // 竞赛详情，补充的参数如下：
     *          array(
     *              'global_ids'    array   题号，array
     *              'problem_hash'  array   global => char
     *          )
     * @throws  LibraryException
     */
    public static function getDetail($params) {
        $id = self::get('id', $params, 0, TYPE_INT_GT0, true);
        return OjContestLogic::getDetail($id);
    }

    /**
     * 从竞赛中添加题目
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
        return OjContestLogic::addProblem($id, $remote, $problemCode);
    }

    /**
     * 从竞赛中移除题目
     *
     * @param   $params
     * @return  int
     * @throws  InterfaceException
     * @throws  LibraryException
     */
    public static function removeProblem($params) {
        $id       = self::get('id', $params, 0, TYPE_INT_GT0, true);
        $globalId = self::get('global_id', $params, 0, TYPE_INT_GT0, true);
        return OjContestLogic::removeProblem($id, $globalId);
    }

    /**
     * 获取比赛中的题目列表
     *
     * @param   $params
     * @return  array   // hash, 题号 => 题目详情，补充的参数如下
     *          array(
     *              'contest_solved'    int     比赛中解决的人数
     *              'contest_submit'    int     比赛中提交的次数
     *          );
     * @throws  InterfaceException
     * @throws  LibraryException
     */
    public static function getProblemHash($params) {
        $id = self::get('id', $params, 0, TYPE_INT_GT0, true);
        return OjContestLogic::getProblemHash($id);
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
        return OjContestLogic::getRankBoard($id);
    }

}