<?php

require_once __DIR__ . '/../model/LogListModel.class.php';

class LoggerLogic {

    public static function getList($field = '*', $where = array(), $order = array(), $limit = -1, $offset = 0) {
        $model = new LogListModel();
        return $model->getList($field, $where, $order, $limit, $offset);
    }

    public static function getCount($where = array()) {
        $model = new LogListModel();
        return $model->getCount($where);
    }

    public static function getRow($field = '*', $where = array(), $order = array(), $offset = 0) {
        $model = new LogListModel();
        return $model->getRow($field, $where, $order, $offset);
    }

    public static function getById($id) {
        $model = new LogListModel();
        return $model->getById($id);
    }

    public static function getByField($field, $value) {
        $model = new LogListModel();
        return $model->getByField($field, $value);
    }

    public static function save($data, $id = 0) {

        $model = new LogListModel();
        if ($id == 0) {
            // 插入
            $id = $model->insert($data);
            return $id;
        } else {
            // 更新
            $affects = $model->updateById($id, $data);
            return $affects;
        }
    }

    public static function deleteById($id) {
        $model  = new LogListModel();
        return $model->deleteById($id);
    }

}