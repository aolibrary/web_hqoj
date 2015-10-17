<?php

require_once __DIR__ . '/../model/OjProblemModel.class.php';

class OjProblemLogic {

    public static function getList($field = '*', $where = array(), $order = array(), $limit = -1, $offset = 0) {
        $model = new OjProblemModel();
        return $model->getList($field, $where, $order, $limit, $offset);
    }

    public static function getCount($where = array()) {
        $model = new OjProblemModel();
        return $model->getCount($where);
    }

    public static function getRow($field = '*', $where = array(), $order = array(), $offset = 0) {
        $model = new OjProblemModel();
        return $model->getRow($field, $where, $order, $offset);
    }

    public static function getById($id) {
        $model = new OjProblemModel();
        return $model->getById($id);
    }

    public static function getByField($field, $value) {
        $model = new OjProblemModel();
        return $model->getByField($field, $value);
    }

    public static function save($data, $id = 0, $trans = null) {

        if (!empty($trans)) {
            $model = new OjProblemModel($trans);
        } else {
            $model = new OjProblemModel();
        }
        if (0 == $id) {

            $userInfo = UserCommonInterface::getById(array('id' => $data['user_id']));
            if (empty($userInfo)) {
                throw new InterfaceException('用户不存在！');
            }

            $history = '';
            if ($data['remote'] == StatusVars::REMOTE_HQU) {
                $dataTime = date('Y-m-d H:i:s', time());
                $history = "<p>{$dataTime} 用户{$userInfo['username']}新建了题目</p>";
            }

            $insertData = array(
                'user_id'       => $data['user_id'],
                'remote'        => $data['remote'],
                'time_limit'    => Arr::get('time_limit', $data, 1000),
                'memory_limit'  => Arr::get('memory_limit', $data, 32768),
                'hidden'        => ($data['remote'] == StatusVars::REMOTE_HQU ? 1 : 0),
                'audit_history' => $history,
            );

            if ($data['remote'] == StatusVars::REMOTE_HQU) {
                // 如果添加是华大题目
                if (!isset($data['problem_id'])) {
                    $where = array(
                        array('remote', '=', $data['remote']),
                    );
                    $lastInfo = self::getRow('MAX(problem_id) AS max_problem_id', $where);
                    $lastId = Arr::get('max_problem_id', $lastInfo, 999);
                    $insertData['problem_id']   = $lastId+1;
                    $insertData['problem_code'] = $lastId+1;
                }
            } else {
                $insertData['problem_id']   = $data['problem_id'];
                $insertData['problem_code'] = $data['problem_code'];
            }

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
        $model  = new OjProblemModel();
        return $model->deleteById($id);
    }

    public static function getDetail($remote, $problemId = '', $problemCode = '') {

        $where = array();
        $where[] = array('remote', '=', $remote);
        if (!empty($problemId)) {
            $where[] = array('problem_id', '=', $problemId);
        }
        if (!empty($problemCode)) {
            $where[] = array('problem_code', '=', $problemCode);
        }
        return self::getRow('*', $where);
    }

    public static function auditHistory($problemId, $appendHistory) {

        $problemInfo = self::getDetail(StatusVars::REMOTE_HQU, $problemId);
        if (empty($problemInfo)) {
            throw new InterfaceException('题目不存在！');
        }
        $history = $problemInfo['audit_history'] . $appendHistory;
        $data = array(
            'audit_history' => $history,
        );
        $model = new OjProblemModel();
        $affects = $model->updateById($problemInfo['id'], $data);
        return $affects;
    }

    public static function show($id) {

        $problemInfo = self::getById($id);
        if (empty($problemInfo)) {
            throw new InterfaceException('题目不存在！');
        }
        // 校验状态
        if (!$problemInfo['hidden']) {
            return 0;
        }

        $model = new OjProblemModel();
        $data = array(
            'hidden' => 0,
        );
        $affected = $model->updateById($id, $data);
        return $affected;
    }

    public static function hide($id) {

        $problemInfo = self::getById($id);
        if (empty($problemInfo)) {
            throw new InterfaceException('题目不存在！');
        }
        // 校验状态
        if ($problemInfo['hidden']) {
            return 0;
        }

        $model = new OjProblemModel();
        $data = array(
            'hidden' => 1,
        );
        $affected = $model->updateById($id, $data);
        return $affected;
    }

    public static function setUser($id, $username) {

        $problemInfo = self::getById($id);
        if (empty($problemInfo)) {
            throw new InterfaceException('题目不存在！');
        }
        $userInfo = UserCommonInterface::getByLoginName(array('login_name' => $username));
        if (empty($userInfo)) {
            throw new InterfaceException('用户不存在！');
        }
        $model = new OjProblemModel();
        $data = array(
            'user_id' => $userInfo['id'],
        );
        $affects = $model->updateById($id, $data);
        return $affects;
    }

    public static function insertAll($dataList) {

        foreach ($dataList as &$dataInfo) {
            $dataInfo['hidden'] = 0;
        }
        $model = new OjProblemModel();
        $ret = $model->insertMulti($dataList);
        return $ret;
    }

}