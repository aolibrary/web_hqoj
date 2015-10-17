<?php

class MyController extends ProjectController {

    public function defaultAction() {

        $username = Request::getGET('username');
        if (empty($username)) {
            $this->renderError();
        }

        // 校验用户
        $userInfo = UserCommonInterface::getByLoginName(array('login_name' => $username));
        if (empty($userInfo)) {
            $this->renderError();
        }

        // 获取solutionList
        $where = array(
            array('user_id', '=', $userInfo['id']),
        );
        $order = array(
            'remote'        => 'ASC',
            'problem_code'  => 'ASC',
        );
        $solutionList = OjSolutionInterface::getList(array(
            'where' => $where,
            'order' => $order,
        ));

        // 计算排名，先按题数，再按照提交次数
        $where = array(
            array('OR' => array(
                array('solved_all', '>', $userInfo['solved_all']),
                array(
                    array('solved_all', '=', $userInfo['solved_all']),
                    array('submit_all', '>', $userInfo['submit_all']),
                ),
                array(
                    array('solved_all', '=', $userInfo['solved_all']),
                    array('submit_all', '=', $userInfo['submit_all']),
                    array('id', '<', $userInfo['id']),
                ),
            )),
        );
        $prevCount = UserCommonInterface::getCount($where);
        $rank = intval($prevCount)+1;

        // 计算解决的题目
        $solvedProblemList = array();
        $visited = array();  // 标记数组
        foreach ($solutionList as $solutionInfo) {
            $remote = $solutionInfo['remote'];
            $problemCode = $solutionInfo['problem_code'];
            if (!isset($visited[$remote][$problemCode]) && $solutionInfo['result'] == StatusVars::ACCEPTED) {
                $problemInfo = array( 'remote' => $remote, 'problem_code' => $problemCode );
                $solvedProblemList[] = $problemInfo;
                $visited[$remote][$problemCode] = 1;
            }
        }

        // 计算未解决的题目
        $unSolvedProblemList = array();
        $visited2 = array();
        foreach ($solutionList as $solutionInfo) {
            $remote = $solutionInfo['remote'];
            $problemCode = $solutionInfo['problem_code'];
            if (!isset($visited2[$remote][$problemCode]) && !isset($visited[$remote][$problemCode])) {
                $problemInfo = array( 'remote' => $remote, 'problem_code' => $problemCode );
                $unSolvedProblemList[] = $problemInfo;
                $visited2[$remote][$problemCode] = 1;
            }
        }

        // 输出
        $this->renderFramework(array(
            'rank'                  => $rank,
            'userInfo'              => $userInfo,
            'solvedProblemList'     => $solvedProblemList,
            'unSolvedProblemList'   => $unSolvedProblemList,
        ), 'user/my.php');
    }
}
