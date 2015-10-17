<?php

require_once __DIR__ . '/../model/OjProblemSetModel.class.php';

class OjProblemSetLogic {

    public static function getList($field = '*', $where = array(), $order = array(), $limit = -1, $offset = 0) {
        $model = new OjProblemSetModel();
        return $model->getList($field, $where, $order, $limit, $offset);
    }

    public static function getCount($where = array()) {
        $model = new OjProblemSetModel();
        return $model->getCount($where);
    }

    public static function getRow($field = '*', $where = array(), $order = array(), $offset = 0) {
        $model = new OjProblemSetModel();
        return $model->getRow($field, $where, $order, $offset);
    }

    public static function getById($id) {
        $model = new OjProblemSetModel();
        return $model->getById($id);
    }

    public static function getByField($field, $value) {
        $model = new OjProblemSetModel();
        return $model->getByField($field, $value);
    }

    public static function save($data, $id = 0) {

        $model = new OjProblemSetModel();
        if (0 == $id) {

            // 对某些字段特殊处理
            $insertData = array(
                'problem_set'   => '[]',
                'refresh_at'    => time(),
                'hidden'        => 1,
                'user_id'       => $data['user_id'],
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
        $model = new OjProblemSetModel();
        return $model->deleteById($id);
    }

    public static function getRankBoard($id) {

        $setInfo = self::getById($id);
        if (empty($setInfo)) {
            throw new InterfaceException('专题不存在！');
        }
        $globalIds = json_decode($setInfo['problem_set'], true);
        if (empty($globalIds)) {
            return array(array(), array(), array());
        }

        // 取出提交记录
        $where = array(
            array('problem_global_id', 'IN', $globalIds),
            array('result', '>=', 4),
        );
        $order = array(
            'id' => 'ASC',
        );
        $solutionList = OjSolutionInterface::getList(array(
            'field' => 'id,user_id,problem_global_id,result',
            'where' => $where,
            'order' => $order,
        ));

        // 计算$rankHash
        $rankHash   = array();
        $mat        = array();
        $firstBlood = array(); // 标记第一滴血
        foreach ($solutionList as $solutionInfo) {
            $globalId = $solutionInfo['problem_global_id'];
            $userId = $solutionInfo['user_id'];
            $result = $solutionInfo['result'];

            if (!isset($rankHash[$userId])) {
                $rankHash[$userId]['solved'] = 0;
                $rankHash[$userId]['all_fail_count'] = 0;
            }
            if (!isset($mat[$userId][$globalId])) {
                $mat[$userId][$globalId] = array();
                $mat[$userId][$globalId]['fail_count'] = 0;
            }

            // 已经通过的，直接continue
            if (isset($mat[$userId][$globalId]['accepted'])) {
                continue;
            }
            // 如果这条solution是accepted
            if ($result == StatusVars::ACCEPTED) {
                $mat[$userId][$globalId]['accepted'] = 1;
                $rankHash[$userId]['solved']++;

                // 标记1血
                if (!isset($firstBlood[$globalId])) {
                    $mat[$userId][$globalId]['first_blood'] = 1;
                    $firstBlood[$globalId] = 1;
                }
                continue;
            } else {
                $mat[$userId][$globalId]['fail_count']++;
                $rankHash[$userId]['all_fail_count']++;
            }
        }

        uasort($rankHash, array('OjProblemSetLogic', 'cmp'));

        // 获取用户信息
        $userIds = array_unique(array_column($solutionList, 'user_id'));
        $userHash = UserCommonInterface::getById(array('id' => $userIds));

        return array($rankHash, $mat, $userHash);
    }

    private static function cmp($a, $b) {
        if ($a['solved'] < $b['solved']
            || $a['solved'] == $b['solved'] && $a['all_fail_count'] >= $b['all_fail_count']) {
            return 1;
        }
        return -1;
    }

    public static function show($id) {

        $setInfo = self::getById($id);
        if (empty($setInfo)) {
            throw new InterfaceException('专题不存在！');
        }
        if (empty($setInfo['title'])) {
            throw new InterfaceException('标题为空，无法公开！');
        }
        if (!$setInfo['hidden']) {
            return 0;
        }
        $data = array(
            'hidden' => 0,
        );
        $model = new OjProblemSetModel();
        $affects = $model->updateById($id, $data);
        return $affects;
    }

    public static function hide($id) {

        $setInfo = self::getById($id);
        if (empty($setInfo)) {
            throw new InterfaceException('专题不存在！');
        }
        if ($setInfo['hidden']) {
            return 0;
        }
        $data = array(
            'hidden' => 1,
        );
        $model = new OjProblemSetModel();
        $affects = $model->updateById($id, $data);
        return $affects;
    }

    public static function addProblem($id, $remote, $problemCode) {

        $setInfo = self::getById($id);
        if (empty($setInfo)) {
            throw new InterfaceException('专题不存在！');
        }
        $problemInfo = OjProblemInterface::getDetail(array(
            'remote'        => $remote,
            'problem_code'  => $problemCode,
        ));
        if (empty($problemInfo)) {
            throw new InterfaceException('题目不存在！');
        }
        $globalIds = (array) json_decode($setInfo['problem_set'], true);
        if (in_array($problemInfo['id'], $globalIds)) {
            return 0;
        }
        if (count($globalIds) >= ContestVars::SET_PROBLEM_LIMIT) {
            throw new InterfaceException('题目数量达到上限！');
        }
        $globalIds[] = (int) $problemInfo['id'];
        $json = json_encode($globalIds);
        $model = new OjProblemSetModel();
        $data = array(
            'problem_set'   => $json,
        );
        $affects = $model->updateById($id, $data);
        return $affects;
    }

    public static function removeProblem($id, $globalId) {

        $setInfo = self::getById($id);
        if (empty($setInfo)) {
            throw new InterfaceException('专题不存在！');
        }
        $globalIds = (array) json_decode($setInfo['problem_set'], true);
        if (!in_array($globalId, $globalIds)) {
            return 0;
        }
        foreach ($globalIds as $i => &$val) {
            if ($val == $globalId) {
                unset($globalIds[$i]);
                break;
            }
        }
        $json = json_encode($globalIds);
        $model = new OjProblemSetModel();
        $data = array(
            'problem_set'   => $json,
        );
        $affects = $model->updateById($id, $data);
        return $affects;
    }

    public static function stick($id) {

        $setInfo = self::getById($id);
        if (empty($setInfo)) {
            throw new InterfaceException('专题不存在！');
        }
        if ($setInfo['listing_status']) {
            return 0;
        }
        $data = array(
            'listing_status' => 1,
        );
        $model = new OjProblemSetModel();
        $affects = $model->updateById($id, $data);
        return $affects;
    }

    public static function cancelStick($id) {

        $setInfo = self::getById($id);
        if (empty($setInfo)) {
            throw new InterfaceException('专题不存在！');
        }
        if (!$setInfo['listing_status']) {
            return 0;
        }
        $data = array(
            'listing_status' => 0,
        );
        $model = new OjProblemSetModel();
        $affects = $model->updateById($id, $data);
        return $affects;
    }

}