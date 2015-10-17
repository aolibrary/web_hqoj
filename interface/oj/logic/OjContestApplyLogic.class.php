<?php

require_once __DIR__ . '/../model/OjContestApplyModel.class.php';

class OjContestApplyLogic {

    public static function getList($field = '*', $where = array(), $order = array(), $limit = -1, $offset = 0) {
        $model = new OjContestApplyModel();
        return $model->getList($field, $where, $order, $limit, $offset);
    }

    public static function getCount($where = array()) {
        $model = new OjContestApplyModel();
        return $model->getCount($where);
    }

    public static function getRow($field = '*', $where = array(), $order = array(), $offset = 0) {
        $model = new OjContestApplyModel();
        return $model->getRow($field, $where, $order, $offset);
    }

    public static function getById($id) {
        $model = new OjContestApplyModel();
        return $model->getById($id);
    }

    public static function getByField($field, $value) {
        $model = new OjContestApplyModel();
        return $model->getByField($field, $value);
    }

    public static function save($data, $id = 0) {

        $model = new OjContestApplyModel();
        if (0 == $id) {

            // 判断是否已经存在
            $applyInfo = self::getDetail($data['contest_id'], $data['user_id']);
            $contestInfo = OjContestInterface::getById(array('id' => $data['contest_id']));
            if (empty($contestInfo)) {
                throw new InterfaceException('竞赛不存在！');
            }
            if ($contestInfo['type'] != ContestVars::TYPE_APPLY) {
                throw new InterfaceException('比赛不需要报名！');
            }
            if ($contestInfo['end_time'] < time()) {
                throw new InterfaceException('比赛已经结束！');
            }

            $data['status'] = ContestVars::APPLY_QUEUE;
            $data['is_diy'] = $contestInfo['is_diy'];

            if (!empty($applyInfo)) {
                if ($applyInfo['status'] == ContestVars::APPLY_ACCEPTED) {
                    throw new InterfaceException('报名已通过，无法修改！');
                }
                $model->updateById($applyInfo['id'], $data);
                return $applyInfo['id'];
            } else {
                $id = $model->insert($data);
                return $id;
            }

        } else {

            // 对某些字段特殊处理
            $updateData = $data;

            $affects = $model->updateById($id, $updateData);
            return $affects;
        }
    }

    public static function deleteById($id) {
        $model = new OjContestApplyModel();
        return $model->deleteById($id);
    }

    public static function accept($id) {

        $applyInfo = self::getById($id);
        if (empty($applyInfo)) {
            throw new InterfaceException('报名信息不存在！');
        }
        if ($applyInfo['status'] == ContestVars::APPLY_ACCEPTED) {
            return 0;
        }

        $model = new OjContestApplyModel();
        $data = array(
            'status'    => ContestVars::APPLY_ACCEPTED,
        );
        $affects = $model->updateById($id, $data);
        return $affects;
    }

    public static function reject($id) {

        $applyInfo = self::getById($id);
        if (empty($applyInfo)) {
            throw new InterfaceException('报名信息不存在！');
        }
        if ($applyInfo['status'] == ContestVars::APPLY_REJECTED) {
            return 0;
        }

        $model = new OjContestApplyModel();
        $data = array(
            'status'    => ContestVars::APPLY_REJECTED,
        );
        $affects = $model->updateById($id, $data);
        return $affects;
    }

    public static function getDetail($contestId, $userId) {

        $where = array(
            array('contest_id', '=', $contestId),
            array('user_id', '=', $userId),
        );
        $applyInfo = self::getRow('*', $where);
        return $applyInfo;
    }

    public static function getLastInfo($userId) {

        $where = array(
            array('user_id', '=', $userId),
        );
        $applyInfo = self::getRow('*', $where);
        return $applyInfo;
    }

}