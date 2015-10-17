<?php

class ListController extends ProjectController {

    public function defaultAction() {

        $problemHash = OjContestInterface::getProblemHash(array('id' => $this->contestInfo['id']));

        // 获取该用户比赛中提交的solutionList
        $where = array(
            array('contest_id', '=', $this->contestInfo['id']),
            array('user_id', '=', $this->loginUserInfo['id']),
        );
        $order = array('id' => 'ASC');
        $solutionList = OjSolutionInterface::getList(array(
            'where' => $where,
            'order' => $order,
        ));

        // 构建hash，globalId => solutionInfo
        $userSolution = array();
        foreach ($solutionList as $solutionInfo) {
            $globalId = $solutionInfo['problem_global_id'];
            if (!array_key_exists($globalId, $userSolution) || $solutionInfo['result'] == StatusVars::ACCEPTED) {
                $userSolution[$globalId] = $solutionInfo;
            }
        }

        $this->renderFramework(array(
            'problemHash'   => $problemHash,
            'userSolution'  => $userSolution,
        ), 'problem/list.php');
    }
}
