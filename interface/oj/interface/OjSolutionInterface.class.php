<?php

require_once __DIR__ . '/../logic/OjSolutionLogic.class.php';

class OjSolutionInterface extends BaseInterface {

    public static function getList($params = array()) {
        $field  = self::get('field', $params, '*', TYPE_STR_Y);
        $where  = self::get('where', $params, array(), TYPE_ARR);
        $order  = self::get('order', $params, array(), TYPE_ARR);
        $limit  = self::get('limit', $params, -1, TYPE_INT);
        $offset = self::get('offset', $params, 0, TYPE_INT);
        $includeContest = self::get('include_contest', $params, false, TYPE_BOOL);   // 是否取出竞赛的提交记录
        return OjSolutionLogic::getList($field, $where, $order, $limit, $offset, $includeContest);
    }

    public static function getCount($params = array()) {
        $where  = $params;
        $includeContest = self::get('include_contest', $params, false, TYPE_BOOL);   // 是否取出竞赛的提交记录
        return OjSolutionLogic::getCount($where, $includeContest);
    }

    public static function getRow($params) {
        $field  = self::get('field', $params, '*', TYPE_STR_Y);
        $where  = self::get('where', $params, array(), TYPE_ARR);
        $order  = self::get('order', $params, array(), TYPE_ARR);
        $offset = self::get('offset', $params, 0, TYPE_INT);
        return OjSolutionLogic::getRow($field, $where, $order, $offset);
    }

    public static function getById($params) {
        $id = self::get('id', $params, 0, TYPE_INT_GT0|TYPE_ARR, true);
        return OjSolutionLogic::getById($id);
    }

    public static function getByField($params) {
        $field = key($params);
        self::check($field, TYPE_STR_Y);
        $value = self::get($field, $params, '', TYPE_NUM|TYPE_STR|TYPE_ARR, true);
        return OjSolutionLogic::getByField($field, $value);
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
        $id    = self::get('id', $params, 0, TYPE_INT);
        $trans = self::get('trans', $params, null, TYPE_OBJ);

        if (0 == $id) {

            // 必传参数
            self::judge($params, array(
                'global_id',
                'user_id',
                'language',
                'source',
            ));

            // 允许插入的字段
            $data = self::getAll($params, array(
                'global_id'     => TYPE_INT_GT0,  // oj_problem表中的id
                'user_id'       => TYPE_INT_GT0,  // 用户id
                'language'      => TYPE_INT,      // 语言
                'source'        => TYPE_STR_Y,    // 源代码
                'contest_id'    => TYPE_INT_GT0,  // 所在竞赛id
            ));

        } else {

            // 允许更新的字段
            $data = self::getAll($params, array(
                'share'         => TYPE_INT,
                'time_cost'     => TYPE_INT,
                'memory_cost'   => TYPE_INT,
                'judge_time'    => TYPE_INT,
                'run_id'        => TYPE_INT,
                'remote_uid'    => TYPE_INT,
                'result'        => TYPE_INT,
            ));

        }

        return OjSolutionLogic::save($data, $id, $trans);
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
        return OjSolutionLogic::deleteById($id);
    }

    /**
     * 5秒内是否提交过
     *
     * @param   $params
     * @return  bool
     * @throws  LibraryException
     */
    public static function submitAlready($params) {
        $userId = self::get('user_id', $params, 0, TYPE_INT_GT0, true);
        return OjSolutionLogic::submitAlready($userId);
    }

    /**
     * 获取solution，包括code, log
     *
     * @param   $params
     * @return  array   补充参数如下：
     *          array(
     *              'has_log'       bool    是否有日志
     *              'detail'        string  detail日志
     *              're'            string  runtime日志
     *              'ce'            string  compile日志
     *              'source'        string  代码
     *              'source_format' string  去除html标签的代码
     * @throws  LibraryException
     */
    public static function getDetail($params) {
        $id = self::get('id', $params, 0, TYPE_INT_GT0, true);
        return OjSolutionLogic::getDetail($id);
    }

    /**
     * 重判
     *
     * @param   $params
     * @return  int     affected_rows
     * @throws  InterfaceException
     * @throws  LibraryException
     */
    public static function rejudge($params) {
        $id = self::get('id', $params, 0, TYPE_INT_GT0, true);
        return OjSolutionLogic::rejudge($id);
    }

}