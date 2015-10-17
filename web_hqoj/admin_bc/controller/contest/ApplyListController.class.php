<?php

class ApplyListController extends ProjectController {

    public function defaultAction() {

        $pageSize = 20;

        // 获取参数
        $page       = Pager::get();
        $status     = (int) Request::getGET('status', -1);
        $contestId  = (int) Request::getGET('contest-id', 0);

        $where = array();
        $where[] = array('is_diy', '=', 0);
        if (!empty($contestId)) {
            $where[] = array('contest_id', '=', $contestId);
        }
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
        $allCount = 0;
        $userHash = array();
        $contestHash = array();
        if (!empty($applyList)) {
            $allCount = OjContestApplyInterface::getCount($where);
            $userIds = array_column($applyList, 'user_id');
            $userHash = UserCommonInterface::getById(array('id' => $userIds));

            $contestIds = array_unique(array_column($applyList, 'contest_id'));
            $contestHash = OjContestInterface::getById(array('id' => $contestIds));
        }

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
            'contestHash'   => $contestHash,
            'userHash'      => $userHash,
        ), 'contest/apply_list.php');
    }

    public function ajaxChangeStatusAction() {

        $applyId = Request::getPOST('apply-id');
        $op      = Request::getPOST('op');

        if (!in_array($op, array(1, 2)) || empty($applyId)) {
            $this->renderError('参数错误！');
        }

        $applyInfo = OjContestApplyInterface::getById(array('id' => $applyId));
        if (empty($applyInfo)) {
            $this->renderError('报名信息不存在！');
        }
        if ($op == 1 && $applyInfo['status'] == ContestVars::APPLY_ACCEPTED
            || $op == 2 && $applyInfo['status'] == ContestVars::APPLY_REJECTED) {
            $msg = ($op == 1 ? '已经通过！' : '已经拒绝！');
            $this->renderError($msg);
        }

        if ($op == 1) {
            OjContestApplyInterface::accept(array('id' => $applyId));
        } else {
            OjContestApplyInterface::reject(array('id' => $applyId));
        }

        $this->setNotice(FrameworkVars::NOTICE_SUCCESS, '操作成功！');
        $this->renderAjax(0);
    }

}
