<?php

class JudgeListController extends ProjectController {

    public function defaultAction() {

        $pageSize = 15;

        $username   = Request::getGET('username');
        $problemId  = Request::getGET('problem-id');
        $language   = (int) Request::getGET('language', -1);
        $result     = (int) Request::getGET('result', -1);

        // 获取id
        $maxId = (int) Request::getGET('max-id', -1);
        $minId = (int) Request::getGET('min-id', -1);

        $userInfo = array();
        if (!empty($userInfo)) {
            $userInfo = UserCommonInterface::getByLoginName(array('login_name' => $username));
        }

        // 构建where
        $where = array();
        if (!empty($username)) {
            $where[] = array('user_id', '=', Arr::get('id', $userInfo, 0));
        }
        if (!empty($problemId)) {
            // HQOJ上的题目，problem_code和problem_id相同
            $where[] = array('problem_id', '=', $problemId);
        }
        if ($language != -1) {
            $where[] = array('language', '=', $language);
        }
        if ($result != -1) {
            $where[] = array('result', '=', $result);
        }

        // 获取judgeList
        if ($maxId != -1) {
            $where[] = array('id', '<=', $maxId);
            $judgeList = OjJudgeInterface::getList(array(
                'where' => $where,
                'order' => array( 'id' => 'DESC' ),
                'limit' => $pageSize,
            ));
        } else if ($minId != -1) {
            $where[] = array('id', '>=', $minId);
            $judgeList = OjJudgeInterface::getList(array(
                'where' => $where,
                'order' => array( 'id' => 'ASC' ),
                'limit' => $pageSize,
            ));
            $judgeList = array_reverse($judgeList, true);
        } else {
            $judgeList = OjJudgeInterface::getList(array(
                'where' => $where,
                'order' => array( 'id' => 'DESC' ),
                'limit' => $pageSize,
            ));
        }

        // 获取userHash
        $userIds = array_unique(array_column($judgeList, 'user_id'));
        $userHash = UserCommonInterface::getById(array('id' => $userIds));

        // 格式化solution
        foreach ($judgeList as &$judgeInfo) {
            $text  = StatusVars::$RESULT_FORMAT[$judgeInfo['result']];
            $class = StatusVars::$RESULT_CLASS[$judgeInfo['result']];
            $judgeInfo['result_html'] = sprintf('<span class="%s">%s</span>', $class, $text);
        }

        // 计算minId, maxId
        $minId = $maxId = 0;
        if (!empty($judgeList)) {
            $judgeIds = array_keys($judgeList);
            $maxId = $judgeIds[0];
            $minId = end($judgeIds);
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
            'judgeList'    => $judgeList,
            'html'         => $html,
        ), 'problem/judge_list.php');
    }

    public function ajaxRejudgeAction() {

        // 获取参数
        $judgeId = Request::getPOST('judge-id');

        // 只能重判自己的solution
        $judgeInfo = OjJudgeInterface::getById(array('id' => $judgeId));
        if (empty($judgeInfo)) {
            $this->renderError('judgeInfo不存在！');
        }

        // rejudge
        OjJudgeInterface::rejudge(array(
            'id'    => $judgeId,
        ));
        $this->renderAjax(0);
    }
}
