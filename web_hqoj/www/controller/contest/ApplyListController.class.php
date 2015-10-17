<?php

class ApplyListController extends ProjectController {

    public function defaultAction() {

        $pageSize = 50;

        // 获取参数
        $page      = Pager::get();
        $contestId = (int) Request::getGET('contest-id');
        $status    = (int) Request::getGET('status', -1);

        $contestInfo = OjContestInterface::getById(array('id' => $contestId));
        if (empty($contestInfo) || $contestInfo['hidden'] || $contestInfo['type'] != ContestVars::TYPE_APPLY) {
            $this->renderError('竞赛不存在，或者竞赛不需要报名！');
        }

        // 构建where
        $where = array();
        $where[] = array('contest_id', '=', $contestId);
        if ($status != -1) {
            $where[] = array('status', '=', $status);
        }

        // 获取数据
        $offset = ($page-1)*$pageSize;
        $applyList = OjContestApplyInterface::getList(array(
            'where'     => $where,
            'limit'     => $pageSize,
            'offset'    => $offset,
        ));
        $allCount = OjContestApplyInterface::getCount($where);

        // userHash
        $userIds = array_unique(array_column($applyList, 'user_id'));
        $userHash = UserCommonInterface::getById(array('id' => $userIds));

        // 缓存部分的html
        $html = array();
        $html['pager'] = $this->view->fetch(array(
            'renderAllCount' => $allCount,
            'renderPageSize' => $pageSize,
            'renderRadius'   => 8,
        ), 'widget/pager.php');

        $this->renderFramework(array(
            'html'          => $html,
            'applyList'     => $applyList,
            'contestInfo'   => $contestInfo,
            'userHash'      => $userHash,
        ), 'contest/apply_list.php');
    }

}
