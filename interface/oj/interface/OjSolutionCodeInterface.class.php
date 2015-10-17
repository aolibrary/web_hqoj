<?php

require_once __DIR__ . '/../logic/OjSolutionCodeLogic.class.php';

class OjSolutionCodeInterface extends BaseInterface {

    public static function getList($params = array()) {
        $field  = self::get('field', $params, '*', TYPE_STR_Y);
        $where  = self::get('where', $params, array(), TYPE_ARR);
        $order  = self::get('order', $params, array(), TYPE_ARR);
        $limit  = self::get('limit', $params, -1, TYPE_INT);
        $offset = self::get('offset', $params, 0, TYPE_INT);
        return OjSolutionCodeLogic::getList($field, $where, $order, $limit, $offset);
    }

    public static function getCount($params = array()) {
        $where  = $params;
        return OjSolutionCodeLogic::getCount($where);
    }

    public static function getRow($params) {
        $field  = self::get('field', $params, '*', TYPE_STR_Y);
        $where  = self::get('where', $params, array(), TYPE_ARR);
        $order  = self::get('order', $params, array(), TYPE_ARR);
        $offset = self::get('offset', $params, 0, TYPE_INT);
        return OjSolutionCodeLogic::getRow($field, $where, $order, $offset);
    }

    public static function getById($params) {
        $id = self::get('id', $params, 0, TYPE_INT_GT0|TYPE_ARR, true);
        return OjSolutionCodeLogic::getById($id);
    }

    public static function getByField($params) {
        $field = key($params);
        self::check($field, TYPE_STR_Y);
        $value = self::get($field, $params, '', TYPE_NUM|TYPE_STR|TYPE_ARR, true);
        return OjSolutionCodeLogic::getByField($field, $value);
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
                'solution_id',
                'source',
            ));

            // 允许插入的字段
            $data = self::getAll($params, array(
                'solution_id' => TYPE_INT_GT0,    // solution_id
                'source'      => TYPE_STR_Y,      // 代码
            ));

        } else {

            // 允许更新的字段
            $data = self::getAll($params, array(

            ));

        }

        return OjSolutionCodeLogic::save($data, $id, $trans);
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
        return OjSolutionCodeLogic::deleteById($id);
    }

}