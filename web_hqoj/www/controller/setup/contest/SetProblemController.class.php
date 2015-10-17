<?php

class SetProblemController extends ProjectBackendController {

    public function defaultAction() {

        $contestId = Request::getGET('contest-id');
        $contestInfo = OjContestInterface::getDetail(array('id' => $contestId));
        if (empty($contestInfo)) {
            $this->renderError('竞赛不存在！');
        }

        // 权限
        if ($contestInfo['user_id'] != $this->loginUserInfo['id']) {
            $this->renderError('你没有权限查看！');
        }

        $problemHash = OjProblemInterface::getById(array('id' => $contestInfo['global_ids']));

        $solutionList = OjSolutionInterface::getList(array(
            'field'     => 'problem_global_id',
            'where'     => array(
                array('contest_id', '=', $contestId),
            ),
            //'include_contest'   => true,
        ));
        $submitGlobalIds = array_unique(array_column($solutionList, 'problem_global_id'));

        $this->renderFramework(array(
            'contestInfo'       => $contestInfo,
            'problemHash'       => $problemHash,
            'globalIds'         => $contestInfo['global_ids'],
            'submitGlobalIds'   => $submitGlobalIds,
        ), 'setup/contest/set_problem.php');
    }

    public function ajaxAddProblemAction() {

        $contestId      = (int) Request::getPOST('contest-id');
        $remote         = (int) Request::getPOST('remote');
        $problemCode    = Request::getPOST('problem-code');

        if (!array_key_exists($remote, StatusVars::$REMOTE_SCHOOL) || empty($problemCode) || empty($contestId)) {
            $this->renderError('缺少参数！');
        }

        // 默认题库
        Cookie::set('default_remote', $remote);

        $contestInfo = OjContestInterface::getDetail(array('id' => $contestId));
        if (empty($contestInfo)) {
            $this->renderError('竞赛不存在！');
        }
        if ($contestInfo['user_id'] != $this->loginUserInfo['id']) {
            $this->renderError('你没有权限！');
        }

        $problemInfo = OjProblemInterface::getDetail(array(
            'remote'        => $remote,
            'problem_code'  => $problemCode,
        ));
        if (empty($problemInfo)) {
            $this->renderError('题目不存在！');
        }
        // 只能添加公开的题目，或者自建私有的题目
        if ($problemInfo['hidden'] && $problemInfo['user_id'] != $this->loginUserInfo['id']) {
            $this->renderError('权限不足，无法添加该题目！');
        }


        $globalIds = $contestInfo['global_ids'];
        if (in_array($problemInfo['id'], $globalIds)) {
            $this->renderError('题目已经添加！');
        }

        // 题目上限
        if (count($globalIds) >= ContestVars::CONTEST_PROBLEM_LIMIT) {
            $this->renderError('题目数量达到上限，无法继续添加！');
        }

        // 更新数据
        OjContestInterface::addProblem(array(
            'id'            => $contestId,
            'remote'        => $remote,
            'problem_code'  => $problemCode,
        ));
        $this->setNotice(FrameworkVars::NOTICE_SUCCESS, '添加成功！');
        $this->renderAjax(0);
    }

    public function ajaxRemoveProblemAction() {

        $contestId      = (int) Request::getPOST('contest-id');
        $globalId       = (int) Request::getPOST('global-id');

        $problemInfo = OjProblemInterface::getById(array('id' => $globalId));
        if (empty($problemInfo)) {
            $this->renderError('题目不存在！');
        }

        $contestInfo = OjContestInterface::getById(array('id' => $contestId));
        if (empty($contestInfo)) {
            $this->renderError('竞赛不存在！');
        }

        // 权限
        if ($contestInfo['user_id'] != $this->loginUserInfo['id']) {
            $this->renderError('你没有权限！');
        }

        // 更新数据
        OjContestInterface::removeProblem(array(
            'id'        => $contestId,
            'global_id' => $globalId,
        ));
        $this->setNotice(FrameworkVars::NOTICE_SUCCESS, '移除成功！');
        $this->renderAjax(0);
    }
}
