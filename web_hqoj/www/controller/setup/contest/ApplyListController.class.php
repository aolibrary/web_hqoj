<?php

class ApplyListController extends ProjectBackendController {

    public function defaultAction() {

        $pageSize = 20;

        // 获取参数
        $page       = Pager::get();
        $status     = (int) Request::getGET('status', -1);
        $contestId  = (int) Request::getGET('contest-id', 0);

        // 获取属于用户的竞赛
        $where = array(
            array('user_id', '=', $this->loginUserInfo['id']),
            array('is_diy', '=', 1),
        );
        $contestHash = OjContestInterface::getList(array(
            'where' => $where,
        ));
        $contestHash = Arr::listToHash('id', $contestHash);
        $contestIds = array_keys($contestHash);

        $userHash = array();
        $applyList = array();
        $allCount = 0;
        if (!empty($contestIds)) {
            if ($contestId > 0 && !in_array($contestId, $contestIds)) {
                $where = false;
            } else if ($contestId > 0 && in_array($contestId, $contestIds)) {
                $where = array(
                    array('contest_id', '=', $contestId),
                );
            } else {
                $where = array(
                    array('contest_id', 'IN', $contestIds),
                );
            }
            if (false !== $where) {
                if ($status != -1) {
                    $where[] = array('status', '=', $status);
                }
                $offset = ($page-1)*$pageSize;
                $applyList = OjContestApplyInterface::getList(array(
                    'where'     => $where,
                    'limit'     => $pageSize,
                    'offset'    => $offset,
                ));
                if (!empty($applyList)) {
                    $allCount = OjContestApplyInterface::getCount($where);
                    $userIds = array_column($applyList, 'user_id');
                    $userHash = UserCommonInterface::getById(array('id' => $userIds));
                }
            }
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
        ), 'setup/contest/apply_list.php');
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

        // 只能处理自己竞赛下的报名
        $where = array(
            array('user_id', '=', $this->loginUserInfo['id']),
            array('is_diy', '=', 1),
        );
        $contestHash = OjContestInterface::getList(array(
            'where' => $where,
        ));
        $contestHash = Arr::listToHash('id', $contestHash);
        $contestIds = array_keys($contestHash);
        if (!in_array($applyInfo['contest_id'], $contestIds)) {
            $this->renderError('你没有权限操作！');
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
