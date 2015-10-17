<?php

require_once __DIR__ . '/../model/OjSolutionLogModel.class.php';

class OjSolutionLogLogic {

    public static function getList($field = '*', $where = array(), $order = array(), $limit = -1, $offset = 0) {
        $model = new OjSolutionLogModel();
        return $model->getList($field, $where, $order, $limit, $offset);
    }

    public static function getCount($where = array()) {
        $model = new OjSolutionLogModel();
        return $model->getCount($where);
    }

    public static function getRow($field = '*', $where = array(), $order = array(), $offset = 0) {
        $model = new OjSolutionLogModel();
        return $model->getRow($field, $where, $order, $offset);
    }

    public static function getById($id) {
        $model = new OjSolutionLogModel();
        return $model->getById($id);
    }

    public static function getByField($field, $value) {
        $model = new OjSolutionLogModel();
        return $model->getByField($field, $value);
    }

    public static function save($data, $id = 0, $trans = null) {

        if (!empty($trans)) {
            $model = new OjSolutionLogModel($trans);
        } else {
            $model = new OjSolutionLogModel();
        }
        if (0 == $id) {

            $logInfo = self::getByField('solution_id', $data['solution_id']);
            if (!empty($logInfo)) {
                $model->updateById($logInfo['id'], $data);
                return $logInfo['id'];
            }

            // 对某些字段特殊处理
            $insertData = $data;

            $id = $model->insert($insertData);
            return $id;
        } else {

            // 对某些字段特殊处理
            $updateData = $data;

            $affects = $model->updateById($id, $updateData);
            return $affects;
        }
    }

    public static function deleteById($id) {
        $model  = new OjSolutionLogModel();
        return $model->deleteById($id);
    }

}