<?php

class JudgeListController extends ProjectBackendController {

    public function defaultAction() {

        $pageSize = 15;

        $problemId  = Request::getGET('problem-id');
        $language   = (int) Request::getGET('language', -1);
        $result     = (int) Request::getGET('result', -1);

        // 获取id
        $maxId = (int) Request::getGET('max-id', -1);
        $minId = (int) Request::getGET('min-id', -1);

        // 获取用户创建的所有题目
        $where = array(
            array('remote', '=', StatusVars::REMOTE_HQU),
            array('user_id', '=', $this->loginUserInfo['id']),
        );
        $problemList = OjProblemInterface::getList(array(
            'field' => 'problem_id',
            'where' => $where,
        ));
        $problemIds = array_column($problemList, 'problem_id');

        // 构建where
        $where = array(
            array('user_id', '=', $this->loginUserInfo['id']),
            array('solution_id', '=', 0),
            array('problem_id', 'IN', $problemIds),
        );
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
        ), 'setup/problem/judge_list.php');
    }

    public function ajaxRejudgeAction() {

        // 获取参数
        $judgeId = Request::getPOST('judge-id');

        // 只能重判自己的solution
        $judgeInfo = OjJudgeInterface::getById(array('id' => $judgeId));
        if (empty($judgeInfo)) {
            $this->renderError('judgeInfo不存在！');
        }
        if ($judgeInfo['solution_id'] > 0 || $judgeInfo['user_id'] != $this->loginUserInfo['id']) {
            $this->renderError('你没有权限重判！');
        }

        // 获取题目
        $problemInfo = OjProblemInterface::getDetail(array(
            'remote'        => StatusVars::REMOTE_HQU,
            'problem_id'    => $judgeInfo['problem_id'],
        ));
        if ($problemInfo['user_id'] != $this->loginUserInfo['id']) {
            $this->renderError('你没有权限重判他人的题目！');
        }

        // rejudge
        OjJudgeInterface::rejudge(array(
            'id'    => $judgeId,
        ));
        $this->renderAjax(0);
    }
}
