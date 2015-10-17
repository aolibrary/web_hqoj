<?php

require_once __DIR__ . '/../model/OjJudgeModel.class.php';

class OjJudgeLogic {

    public static function getList($field = '*', $where = array(), $order = array(), $limit = -1, $offset = 0) {
        $model = new OjJudgeModel();
        return $model->getList($field, $where, $order, $limit, $offset);
    }

    public static function getCount($where = array()) {
        $model = new OjJudgeModel();
        return $model->getCount($where);
    }

    public static function getRow($field = '*', $where = array(), $order = array(), $offset = 0) {
        $model = new OjJudgeModel();
        return $model->getRow($field, $where, $order, $offset);
    }

    public static function getById($id) {
        $model = new OjJudgeModel();
        return $model->getById($id);
    }

    public static function getByField($field, $value) {
        $model = new OjJudgeModel();
        return $model->getByField($field, $value);
    }

    public static function save($data, $id = 0, $trans = null) {

        if (!empty($trans)) {
            $model = new OjJudgeModel($trans);
        } else {
            $model = new OjJudgeModel();
        }
        if (0 == $id) {

            // 校验
            $problemInfo = OjProblemInterface::getDetail(array(
                'remote' => StatusVars::REMOTE_HQU,
                'problem_id' => $data['problem_id'],
            ));

            if (empty($problemInfo)) {
                throw new InterfaceException('题目不存在！');
            }
            if (!array_key_exists($data['language'], StatusVars::$LANGUAGE_SUPPORT[$problemInfo['remote']])) {
                throw new InterfaceException('编译器不支持！');
            }

            // 对某些字段特殊处理
            $insertData = array(
                'problem_id'   => $problemInfo['problem_id'],
                'language'     => $data['language'],
                'time_limit'   => $problemInfo['time_limit'],
                'memory_limit' => $problemInfo['memory_limit'],
                'source'       => $data['source'],
                'result'       => StatusVars::QUEUE,
                'code_length'  => strlen($data['source']),
                'solution_id'  => Arr::get('solution_id', $data, 0),
                'user_id'      => $data['user_id'],
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
        $model = new OjJudgeModel();
        return $model->deleteById($id);
    }

    public static function rejudge($id, $trans = null) {

        if (!empty($trans)) {
            $model = new OjJudgeModel($trans);
        } else {
            $model = new OjJudgeModel();
        }

        // 校验
        $judgeInfo = self::getById($id);
        if (empty($judgeInfo)) {
            throw new InterfaceException('judgeInfo不存在！');
        }

        $problemInfo = OjProblemInterface::getDetail(array(
            'remote'        => StatusVars::REMOTE_HQU,
            'problem_id'    => $judgeInfo['problem_id'],
        ));

        // 更新
        $data = array(
            'time_limit'    => $problemInfo['time_limit'],
            'memory_limit'  => $problemInfo['memory_limit'],
            'judge_time'    => 0,
            'result'        => StatusVars::REJUDGE,
            'time_cost'     => 0,
            'memory_cost'   => 0,
            'ce'            => '',
            're'            => '',
            'detail'        => '',
        );
        $affects = $model->updateById($id, $data);
        return $affects;
    }

}