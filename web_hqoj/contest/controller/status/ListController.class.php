<?php

class ListController extends ProjectController {

    public function defaultAction() {

        $pageSize = 15;

        // 获取参数
        $username    = Request::getGET('username');
        $language    = (int) Request::getGET('language', -1);
        $result      = (int) Request::getGET('result', -1);
        $problemHash = Request::getGET('problem-hash');
        $maxId       = (int) Request::getGET('max-id', -1);
        $minId       = (int) Request::getGET('min-id', -1);

        $globalId = array_search($problemHash, $this->contestInfo['problem_hash']);

        // 获取userInfo，username转换为userId
        $userInfo = array();
        if (!empty($username)) {
            $userInfo = UserCommonInterface::getByLoginName(array('login_name' => $username));
        }

        // 构建where
        $where = array();
        $where[] = array('contest_id', '=', $this->contestInfo['id']);
        $where[] = array('hidden', '=', 0);
        $where[] = array('problem_global_id', 'IN', $this->contestInfo['global_ids']);

        if (!empty($username)) {
            $where[] = array('user_id', '=', Arr::get('id', $userInfo, 0));
        }
        if (!empty($globalId)) {
            $where[] = array('problem_global_id', '=', $globalId);
        }
        if ($language != -1) {
            $where[] = array('language', '=', $language);
        }
        if ($result != -1) {
            $where[] = array('result', '=', $result);
        }

        // 获取solutionList
        if ($maxId != -1) {
            $where[] = array('solution_id', '<=', $maxId);
            $solutionList = OjSolutionInterface::getList(array(
                'where' => $where,
                'order' => array('id' => 'DESC'),
                'limit' => $pageSize,
                'include_contest' => true,
            ));
        } else if ($minId != -1) {
            $where[] = array('solution_id', '>=', $minId);
            $solutionList = OjSolutionInterface::getList(array(
                'where' => $where,
                'order' => array('id' => 'ASC'),
                'limit' => $pageSize,
                'include_contest' => true,
            ));
            $solutionList = array_reverse($solutionList, true);
        } else {
            $solutionList = OjSolutionInterface::getList(array(
                'where' => $where,
                'order' => array('id' => 'DESC'),
                'limit' => $pageSize,
                'include_contest' => true,
            ));
        }

        // 获取userHash
        $userIds = array_unique(array_column($solutionList, 'user_id'));
        $userHash = UserCommonInterface::getById(array('id' => $userIds));

        // 格式化solution
        foreach ($solutionList as &$solutionInfo) {
            $solutionInfo['permission'] = false;
            if ($this->isContestAdmin || $solutionInfo['user_id'] == $this->loginUserInfo['id']) {
                $solutionInfo['permission'] = true;
            }
            $solutionInfo['has_log'] = OjSolutionHelper::hasLog($solutionInfo);
        }

        // 如果是报名，获取报名列表
        $applyHash = array();
        if ($this->contestInfo['type'] == ContestVars::TYPE_APPLY) {
            $where = array(
                array('contest_id', '=', $this->contestInfo['id']),
            );
            $applyHash = OjContestApplyInterface::getList(array(
                'where' => $where,
            ));
            $applyHash = Arr::listToHash('user_id', $applyHash);
        }

        // 计算minId, maxId
        $minId = $maxId = 0;
        if (!empty($solutionList)) {
            $solutionIds = array_keys($solutionList);
            $maxId = $solutionIds[0];
            $minId = end($solutionIds);
        }

        // 缓存html
        $html = array();
        $html['pager'] = $this->view->fetch(array(
            'renderMaxId' => $maxId,
            'renderMinId' => $minId,
        ), 'widget/pager_status.php');

        // render
        $this->renderFramework(array(
            'userHash'     => $userHash,
            'solutionList' => $solutionList,
            'html'         => $html,
            'applyHash'    => $applyHash,
        ), 'status/list.php');
    }

    public function ajaxRejudgeAction() {

        // 获取参数
        $solutionId = (int) Request::getPOST('solution-id');
        $solutionInfo = OjSolutionInterface::getById(array('id' => $solutionId));
        if (empty($solutionInfo)) {
            $this->renderError('Solution不存在！');
        }

        // 竞赛管理员才可以重判，或者timeout
        if (!$this->isContestAdmin && $solutionInfo['result'] != StatusVars::TIME_OUT) {
            $this->renderError('你没有权限重判！');
        }

        // 重判
        OjSolutionInterface::rejudge(array('id' => $solutionId));
        $this->renderAjax(0);
    }

}
