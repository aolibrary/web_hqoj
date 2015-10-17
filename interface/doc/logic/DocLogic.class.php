<?php

require_once __DIR__ . '/../model/DocListModel.class.php';

class DocLogic {

    public static function getList($field = '*', $where = array(), $order = array(), $limit = -1, $offset = 0) {
        $model = new DocListModel();
        return $model->getList($field, $where, $order, $limit, $offset);
    }

    public static function getCount($where = array()) {
        $model = new DocListModel();
        return $model->getCount($where);
    }

    public static function getRow($field = '*', $where = array(), $order = array(), $offset = 0) {
        $model = new DocListModel();
        return $model->getRow($field, $where, $order, $offset);
    }

    public static function getById($id) {
        $model = new DocListModel();
        return $model->getById($id);
    }

    public static function getByField($field, $value) {
        $model = new DocListModel();
        return $model->getByField($field, $value);
    }

    public static function save($data, $id = 0) {

        $model = new DocListModel();
        if (0 == $id) {

            // 对某些字段特殊处理
            $insertData = array(
                'user_id'   => $data['user_id'],
                'category'  => 0,
                'hidden'    => 1,
            );

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
        $model = new DocListModel();
        return $model->deleteById($id);
    }

    public static function show($id) {

        $docInfo = self::getById($id);
        if (empty($docInfo)) {
            throw new InterfaceException('文章不存在！');
        }
        if (!$docInfo['hidden']) {
            return 0;
        }
        $model = new DocListModel();
        $data = array(
            'hidden' => 0,
        );
        $affects = $model->updateById($id, $data);
        return $affects;
    }

    public static function hide($id) {

        $docInfo = self::getById($id);
        if (empty($docInfo)) {
            throw new InterfaceException('文章不存在！');
        }
        if ($docInfo['hidden']) {
            return 0;
        }
        $model = new DocListModel();
        $data = array(
            'hidden' => 1,
        );
        $affects = $model->updateById($id, $data);
        return $affects;
    }

    public static function change($id, $username = '', $category = -1) {

        if (empty($username) && $category == -1) {
            return 0;
        }

        // 校验doc
        $docInfo = self::getById($id);
        if (empty($docInfo)) {
            throw new InterfaceException('文章不存在！');
        }

        $data = array();
        if ($category != -1) {
            if (!array_key_exists($category, DocVars::$CATEGORY)) {
                throw new InterfaceException('类别不存在！');
            }
            $data['category'] = $category;
        }

        if (!empty($username)) {
            // 校验用户
            $userInfo = UserCommonInterface::getByLoginName(array('login_name' => $username));
            if (empty($userInfo)) {
                throw new InterfaceException('用户不存在！');
            }
            $data['user_id'] = $userInfo['id'];
        }

        $model = new DocListModel();
        $affects = $model->updateById($id, $data);
        return $affects;
    }

}