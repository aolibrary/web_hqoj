<?php

class ProblemController extends ProjectController {

    public function defaultAction() {

        $setId = (int) Request::getGET('set-id');
        if (empty($setId)) {
            $this->renderError();
        }

        $setInfo = OjProblemSetInterface::getById(array('id' => $setId));
        if (empty($setInfo)) {
            $this->renderError();
        }

        if (!$this->isOjAdmin && $setInfo['hidden'] && $this->loginUserInfo['id'] != $setInfo['user_id']) {
            Cookie::delete('current_set');
            $this->renderError('您没有权限查看！');
        }

        // 设置当前的set
        Cookie::set('current_set', $setId);

        $problemJson = $setInfo['problem_set'];
        $globalIds = (array) json_decode($problemJson, true);

        // 按照$globalIds顺序
        $problemList = OjProblemInterface::getById(array('id' => $globalIds));

        // 获取用户解决的题目
        $userSolution = array();
        if ($this->loginUserInfo) {
            $where = array(
                array('problem_global_id', 'IN', $globalIds),
                array('user_id', '=', $this->loginUserInfo['id']),
            );
            $solutionList = OjSolutionInterface::getList(array(
                'where' => $where,
            ));
            foreach ($solutionList as $solutionInfo) {
                $globalId = $solutionInfo['problem_global_id'];
                if (!array_key_exists($globalId, $userSolution) || $solutionInfo['result'] == StatusVars::ACCEPTED) {
                    $userSolution[$globalId] = $solutionInfo;
                }
            }
        }

        // userInfo
        $userInfo = UserCommonInterface::getById(array('id' => $setInfo['user_id']));

        $this->renderFramework(array(
            'setInfo'       => $setInfo,
            'problemList'   => $problemList,
            'userSolution'  => $userSolution,
            'userInfo'      => $userInfo,
        ), 'set/set_problem.php');
    }
}
