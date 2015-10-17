<?php

require_once __DIR__ . '/../model/OjContestModel.class.php';

class OjContestLogic {

    public static function getList($field = '*', $where = array(), $order = array(), $limit = -1, $offcontest = 0) {
        $model = new OjContestModel();
        return $model->getList($field, $where, $order, $limit, $offcontest);
    }

    public static function getCount($where = array()) {
        $model = new OjContestModel();
        return $model->getCount($where);
    }

    public static function getRow($field = '*', $where = array(), $order = array(), $offcontest = 0) {
        $model = new OjContestModel();
        return $model->getRow($field, $where, $order, $offcontest);
    }

    public static function getById($id) {
        $model = new OjContestModel();
        return $model->getById($id);
    }

    public static function getByField($field, $value) {
        $model = new OjContestModel();
        return $model->getByField($field, $value);
    }

    public static function save($data, $id = 0, $trans = null) {

        if (!empty($trans)) {
            $model = new OjContestModel($trans);
        } else {
            $model = new OjContestModel();
        }
        if (0 == $id) {

            $insertData = array(
                'user_id'   => $data['user_id'],
                'is_diy'    => empty($data['is_diy']) ? 0 : 1,
                'hidden'    => 1,
            );

            $id = $model->insert($insertData);
            return $id;
        } else {

            if (array_key_exists('is_active', $data)) {
                $data['is_active'] = !empty($data['is_active']) ? 1 : 0;
            }

            // 对某些字段特殊处理
            $updateData = $data;

            $affects = $model->updateById($id, $updateData);
            return $affects;
        }
    }

    public static function deleteById($id) {
        $model = new OjContestModel();
        return $model->deleteById($id);
    }

    public static function show($id) {

        $contestInfo = self::getById($id);
        if (empty($contestInfo)) {
            throw new InterfaceException('竞赛不存在！');
        }
        if (!$contestInfo['hidden']) {
            return 0;
        }
        // 校验是否可以显示
        if (empty($contestInfo['type']) || empty($contestInfo['title'])) {
            throw new InterfaceException('竞赛信息不完整，无法显示！');
        }
        $data = array(
            'hidden'    => 0,
        );
        $model = new OjContestModel();
        $affects = $model->updateById($id, $data);
        return $affects;
    }

    public static function hide($id) {

        $contestInfo = self::getById($id);
        if (empty($contestInfo)) {
            throw new InterfaceException('竞赛不存在！');
        }
        if ($contestInfo['hidden']) {
            return 0;
        }
        $data = array(
            'hidden'    => 1,
        );
        $model = new OjContestModel();
        $affects = $model->updateById($id, $data);
        return $affects;
    }

    public static function getDetail($id) {

        $contestInfo = self::getById($id);
        if (empty($contestInfo)) {
            return array();
        }
        $contestInfo['global_ids'] = (array) json_decode($contestInfo['problem_json'], true);
        $contestInfo['problem_hash'] = array();
        $i = 0;
        foreach ($contestInfo['global_ids'] as $globalId) {
            $contestInfo['problem_hash'][$globalId] = chr($i+65);
            $i++;
        }
        return $contestInfo;
    }

    public static function addProblem($id, $remote, $problemCode) {

        $contestInfo = self::getDetail($id);
        if (empty($contestInfo)) {
            throw new InterfaceException('竞赛不存在！');
        }
        $problemInfo = OjProblemInterface::getDetail(array(
            'remote'        => $remote,
            'problem_code'  => $problemCode,
        ));
        if (empty($problemInfo)) {
            throw new InterfaceException('题目不存在！');
        }
        $globalIds = $contestInfo['global_ids'];
        if (in_array($problemInfo['id'], $globalIds)) {
            return 0;
        }
        if (count($globalIds) >= ContestVars::CONTEST_PROBLEM_LIMIT) {
            throw new InterfaceException('题目数量达到上限！');
        }
        $globalIds[] = (int) $problemInfo['id'];
        $json = json_encode($globalIds);
        $model = new OjContestModel();
        $data = array(
            'problem_json' => $json,
        );
        $affects = $model->updateById($id, $data);

        // 更新排行榜，可能有人提交的题目被恢复
        // TODO

        return $affects;
    }

    public static function removeProblem($id, $globalId) {

        $contestInfo = self::getDetail($id);
        if (empty($contestInfo)) {
            throw new InterfaceException('竞赛不存在！');
        }

        $globalIds = $contestInfo['global_ids'];
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
        $model = new OjContestModel();
        $data = array(
            'problem_json' => $json,
        );
        $affects = $model->updateById($id, $data);

        // 更新排行榜
        // TODO

        return $affects;
    }

    public static function getProblemHash($id) {

        $contestInfo = self::getDetail($id);
        if (empty($contestInfo)) {
            throw new InterfaceException('比赛不存在！');
        }

        // 获取problemList
        $globalIds = $contestInfo['global_ids'];
        $problemHash = OjProblemInterface::getById(array('id' => $globalIds));

        // 重置solved, submit
        foreach ($problemHash as &$problemInfo) {
            $problemInfo['contest_solved'] = $problemInfo['contest_submit'] = 0;
        }

        // 获取solutionList
        $field = 'id,user_id,problem_global_id,result';
        $where = array(
            array('contest_id', '=', $id),
            array('problem_global_id', 'IN', $globalIds),
        );
        $order = array('id' => 'ASC');
        $solutionList = OjSolutionInterface::getList(array(
            'field' => $field,
            'where' => $where,
            'order' => $order,
            'include_contest'   => true,
        ));

        // 计算solved, submit
        $userAccept = array();  // 标记
        foreach ($solutionList as $solutionInfo) {
            $globalId = $solutionInfo['problem_global_id'];
            $userId = $solutionInfo['user_id'];
            $result = $solutionInfo['result'];
            $problemHash[$globalId]['contest_submit']++;
            if (!isset($userAccept[$userId])) {
                $userAccept[$userId] = array();
            }
            if (!isset($userAccept[$userId][$globalId]) && $result == StatusVars::ACCEPTED) {
                $userAccept[$userId][$globalId] = 1;
                $problemHash[$globalId]['contest_solved']++;
            }
        }

        return $problemHash;
    }

    public static function getRankBoard($id) {

        $contestInfo = self::getDetail($id);
        if (empty($contestInfo)) {
            throw new InterfaceException('专题不存在！');
        }

        // 取出提交记录
        $where = array(
            array('contest_id', '=', $id),
            array('result', '>=', 4),
            array('problem_global_id', 'IN', $contestInfo['global_ids']),
        );
        $order = array(
            'id' => 'ASC',
        );
        $solutionList = OjSolutionInterface::getList(array(
            'field' => 'id,user_id,problem_global_id,result,contest_submit_second',
            'where' => $where,
            'order' => $order,
            'include_contest' => true,
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
                $rankHash[$userId]['cost_second'] = 0;
            }
            if (!isset($mat[$userId][$globalId])) {
                $mat[$userId][$globalId] = array(
                    'accepted'      => 0,
                    'first_blood'   => 0,
                    'fail_count'    => 0,
                    'pass_second'   => 0
                );
            }

            // 已经通过的，直接continue
            if ($mat[$userId][$globalId]['accepted']) {
                continue;
            }
            // 如果这条solution是accepted
            if ($result == StatusVars::ACCEPTED) {

                // 记录到mat
                $mat[$userId][$globalId]['accepted'] = 1;
                $mat[$userId][$globalId]['pass_second'] = $solutionInfo['contest_submit_second'];

                if (!isset($firstBlood[$globalId])) {
                    $mat[$userId][$globalId]['first_blood'] = 1;
                    $firstBlood[$globalId] = 1;
                }

                // 记录到rankHash
                $rankHash[$userId]['solved']++;
                $rankHash[$userId]['cost_second'] += $mat[$userId][$globalId]['pass_second']+$mat[$userId][$globalId]['fail_count']*20*60;
                continue;
            } else {
                $mat[$userId][$globalId]['fail_count']++;
            }
        }

        uasort($rankHash, array('OjContestLogic', 'cmp'));

        // 获取用户信息
        $userIds = array_unique(array_column($solutionList, 'user_id'));
        $userHash = UserCommonInterface::getById(array('id' => $userIds));

        return array($rankHash, $mat, $userHash);
    }

    private static function cmp($a, $b) {
        if ($a['solved'] < $b['solved']
            || $a['solved'] == $b['solved'] && $a['cost_second'] >= $b['cost_second']) {
            return 1;
        }
        return -1;
    }

}