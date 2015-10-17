<?php

require_once __DIR__ . '/../logic/OjProblemLogic.class.php';

class OjProblemInterface extends BaseInterface {

    public static function getList($params = array()) {
        $field  = self::get('field', $params, '*', TYPE_STR_Y);
        $where  = self::get('where', $params, array(), TYPE_ARR);
        $order  = self::get('order', $params, array(), TYPE_ARR);
        $limit  = self::get('limit', $params, -1, TYPE_INT);
        $offset = self::get('offset', $params, 0, TYPE_INT);
        return OjProblemLogic::getList($field, $where, $order, $limit, $offset);
    }

    public static function getCount($params = array()) {
        $where  = $params;
        return OjProblemLogic::getCount($where);
    }

    public static function getRow($params) {
        $field  = self::get('field', $params, '*', TYPE_STR_Y);
        $where  = self::get('where', $params, array(), TYPE_ARR);
        $order  = self::get('order', $params, array(), TYPE_ARR);
        $offset = self::get('offset', $params, 0, TYPE_INT);
        return OjProblemLogic::getRow($field, $where, $order, $offset);
    }

    public static function getById($params) {
        $id = self::get('id', $params, 0, TYPE_INT_GT0|TYPE_ARR, true);
        return OjProblemLogic::getById($id);
    }

    public static function getByField($params) {
        $field = key($params);
        self::check($field, TYPE_STR_Y);
        $value = self::get($field, $params, '', TYPE_NUM|TYPE_STR|TYPE_ARR, true);
        return OjProblemLogic::getByField($field, $value);
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
                'remote',
                'user_id',
            ));
            if ($params['remote'] != StatusVars::REMOTE_HQU) {
                self::judge($params, array(
                    'problem_id',
                    'problem_code',
                ));
            }

            // 允许插入的字段
            $data = self::getAll($params, array(
                'remote'        => TYPE_INT,
                'problem_id'    => TYPE_STR,
                'problem_code'  => TYPE_STR,
                'user_id'       => TYPE_INT_GT0,
            ));

        } else {

            // 允许更新的字段
            $data = self::getAll($params, array(
                'title'         => TYPE_STR,
                'source'        => TYPE_STR,
                'time_limit'    => TYPE_INT,
                'memory_limit'  => TYPE_INT,
                'description'   => TYPE_STR,
                'input'         => TYPE_STR,
                'output'        => TYPE_STR,
                'sample_input'  => TYPE_STR,
                'sample_output' => TYPE_STR,
                'hint'          => TYPE_STR,
                'submit'        => TYPE_INT,
                'solved'        => TYPE_INT,
            ));

        }

        return OjProblemLogic::save($data, $id, $trans);
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
        return OjProblemLogic::deleteById($id);
    }

    /**
     * 获取题目详情
     *
     * @param   $params
     * @return  array
     * @throws  LibraryException
     * @throws  InterfaceException
     */
    public static function getDetail($params) {
        $remote      = self::get('remote', $params, 0, TYPE_INT, true);
        $problemId   = self::get('problem_id', $params, '', TYPE_STR_Y);
        $problemCode = self::get('problem_code', $params, '', TYPE_STR_Y);
        if (empty($problemId) && empty($problemCode)) {
            throw new InterfaceException('缺少参数！');
        }
        return OjProblemLogic::getDetail($remote, $problemId, $problemCode);
    }

    /**
     * 添加题目修改历史
     *
     * @param   $params
     * @return  int
     * @throws  InterfaceException
     */
    public static function auditHistory($params) {
        $problemId     = self::get('problem_id', $params, '', TYPE_STR_Y);
        $appendHistory = self::get('append_history', $params, '', TYPE_STR_Y);
        return OjProblemLogic::auditHistory($problemId, $appendHistory);
    }

    /**
     * 将题目显示
     *
     * @param   $params
     * @return  int
     * @throws  InterfaceException
     */
    public static function show($params) {
        $id = self::get('id', $params, 0, TYPE_INT_GT0, true);
        return OjProblemLogic::show($id);
    }

    /**
     * 将题目隐藏
     *
     * @param   $params
     * @return  int
     * @throws  InterfaceException
     */
    public static function hide($params) {
        $id = self::get('id', $params, 0, TYPE_INT_GT0, true);
        return OjProblemLogic::hide($id);
    }

    /**
     * 改变题目的所有者
     *
     * @param   $params
     * @return  int
     * @throws  InterfaceException
     */
    public static function setUser($params) {
        $id       = self::get('id', $params, 0, TYPE_INT_GT0, true);
        $username = self::get('username', $params, '', TYPE_STR_Y, true);
        return OjProblemLogic::setUser($id, $username);
    }

    public static function insertAll($params) {
        $dataList = $params;
        return OjProblemLogic::insertAll($dataList);
    }
}