<?php

class ProblemController extends ProjectBackendController {

    public function defaultAction() {

        $setId = (int) Request::getGET('set-id');
        if (empty($setId)) {
            $this->renderError('参数错误');
        }

        $setInfo = OjProblemSetInterface::getById(array('id' => $setId));
        if (empty($setInfo)) {
            $this->renderError();
        }

        $globalIds = (array) json_decode($setInfo['problem_set'], true);
        $problemHash = OjProblemInterface::getById(array('id' => $globalIds));

        $this->renderFramework(array(
            'setInfo'       => $setInfo,
            'problemHash'   => $problemHash,
        ), 'setup/set/set_problem.php');
    }

    public function ajaxAddProblemAction() {

        $setId       = Request::getPOST('set-id');
        $remote      = Request::getPOST('remote');
        $problemCode = Request::getPOST('problem-code');

        // 默认题库
        Cookie::set('default_remote', $remote);

        $problemInfo = OjProblemInterface::getDetail(array(
            'remote'       => $remote,
            'problem_code' => $problemCode,
        ));
        if (empty($problemInfo)) {
            $this->renderError('题目不存在！');
        }
        if ($problemInfo['hidden']) {
            $this->renderError('无法添加隐藏的题目！');
        }

        $setInfo = OjProblemSetInterface::getById(array('id' => $setId));
        if (empty($setInfo)) {
            $this->renderError('专题不存在！');
        }

        // 属主验证
        if ($this->loginUserInfo['id'] != $setInfo['user_id']) {
            $this->renderError('你没有权限操作！');
        }

        $globalIds = (array) json_decode($setInfo['problem_set'], true);
        if (in_array($problemInfo['id'], $globalIds)) {
            $this->renderError('题目已经在专题中！');
        }

        // 题目上限
        if (count($globalIds) >= ContestVars::SET_PROBLEM_LIMIT) {
            $this->renderError('题目数量达到上限，无法继续添加！');
        }

        // 更新数据
        OjProblemSetInterface::addProblem(array(
            'id'            => $setId,
            'remote'        => $remote,
            'problem_code'  => $problemCode,
        ));
        $this->setNotice(FrameworkVars::NOTICE_SUCCESS, '操作成功');
        $this->renderAjax(0);
    }

    public function ajaxRemoveProblemAction() {

        $setId      = (int) Request::getPOST('set-id');
        $globalId   = (int) Request::getPOST('global-id');
        if (empty($setId) || empty($globalId)) {
            $this->renderError('参数错误！');
        }

        $setInfo = OjProblemSetInterface::getById(array('id' => $setId));
        if (empty($setInfo)) {
            $this->renderError('专题不存在！');
        }

        // 属主验证
        if ($this->loginUserInfo['id'] != $setInfo['user_id']) {
            $this->renderError('你没有权限操作！');
        }

        OjProblemSetInterface::removeProblem(array(
            'id'        => $setId,
            'global_id' => $globalId,
        ));
        $this->setNotice(FrameworkVars::NOTICE_SUCCESS, '操作成功');
        $this->renderAjax(0);
    }
}
