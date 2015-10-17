<?php

class ListController extends ProjectBackendController {

    public function defaultAction() {

        $pageSize = 20;

        // 获取参数
        $page   = Pager::get();
        $title  = Request::getGET('title');
        $status = (int) Request::getGET('status');

        // 构建where
        $where = array(
            array('user_id', '=', $this->loginUserInfo['id']),
            array('is_diy', '=', 1),
        );
        if (!empty($status) && $status != -1) {
            $where[] = array('hidden', '=', $status - 1);
        }
        if (!empty($title)) {
            $where[] = array('title', 'LIKE', "%{$title}%");
        }

        // 获取数据
        $offset = ($page-1)*$pageSize;
        $contestList = OjContestInterface::getList(array(
            'where'     => $where,
            'limit'     => $pageSize,
            'offset'    => $offset,
        ));
        $allCount = 0;
        if (!empty($contestList)) {
            $allCount = OjContestInterface::getCount($where);
        }

        $userIds  = array_unique(array_column($contestList, 'user_id'));
        $userHash = UserCommonInterface::getById(array('id' => $userIds));

        // 缓存部分的html
        $html = array();
        $html['pager'] = $this->view->fetch(array(
            'renderAllCount' => $allCount,
            'renderPageSize' => $pageSize,
            'renderRadius'   => 8,
        ), 'widget/pager.php');

        // 输出
        $this->renderFramework(array(
            'contestList' => $contestList,
            'userHash'    => $userHash,
            'html'        => $html,
        ), 'setup/contest/list.php');
    }

    public function ajaxShowAction() {

        $contestId = Request::getPOST('contest-id');
        $contestInfo = OjContestInterface::getById(array('id' => $contestId));
        if (empty($contestInfo)) {
            $this->renderError('竞赛不存在！');
        }

        // 权限
        if ($contestInfo['user_id'] != $this->loginUserInfo['id']) {
            $this->renderError('你没有操作权限！');
        }

        if (! $contestInfo['hidden']) {
            $this->renderError('竞赛已经显示！');
        }

        // 校验是否可以显示
        if (empty($contestInfo['type']) || empty($contestInfo['title'])) {
            $this->renderError('竞赛信息不完整，无法显示！');
        }

        OjContestInterface::show(array('id' => $contestId));
        $this->setNotice(FrameworkVars::NOTICE_SUCCESS, '操作成功！');
        $this->renderAjax(0);
    }

    public function ajaxHideAction() {

        $contestId = Request::getPOST('contest-id');
        $contestInfo = OjContestInterface::getById(array('id' => $contestId));
        if (empty($contestInfo)) {
            $this->renderError('竞赛不存在！');
        }

        // 权限
        if ($contestInfo['user_id'] != $this->loginUserInfo['id']) {
            $this->renderError('你没有权限！');
        }

        if ($contestInfo['hidden']) {
            $this->renderError('竞赛已经隐藏！');
        }

        OjContestInterface::hide(array('id' => $contestId));
        $this->setNotice(FrameworkVars::NOTICE_SUCCESS, '操作成功！');
        $this->renderAjax(0);
    }
}
