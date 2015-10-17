<?php

class ListController extends ProjectController {

    public function defaultAction() {

        $pageSize = 20;

        // 获取参数
        $page   = Pager::get();
        $remote = (int) Request::getGET('remote', StatusVars::REMOTE_HQU);
        $status = (int) Request::getGET('status');

        // 构建where
        $where = array();
        $where[] = array('remote', '=', $remote);
        if (!empty($status) && $status != -1) {
            $where[] = array('hidden', '=', $status - 1);
        }

        // 获取数据
        $offset = ($page-1)*$pageSize;
        $problemList = OjProblemInterface::getList(array(
            'where'     => $where,
            'limit'     => $pageSize,
            'offset'    => $offset,
        ));
        $allCount = 0;
        $userHash = array();
        if (!empty($problemList)) {
            $allCount = OjProblemInterface::getCount($where);
            $userIds = array_unique(array_column($problemList, 'user_id'));
            $userHash = UserCommonInterface::getById(array('id' => $userIds));
        }

        // 缓存部分的html
        $html = array();
        $html['pager'] = $this->view->fetch(array(
            'renderAllCount' => $allCount,
            'renderPageSize' => $pageSize,
            'renderRadius'   => 5,
        ), 'widget/pager.php');

        // 输出
        $this->renderFramework(array(
            'problemList'   => $problemList,
            'userHash'      => $userHash,
            'html'          => $html,
        ), 'problem/list.php');
    }

    public function iframeShowHistoryAction() {

        $problemId = Request::getGET('problem-id');
        if (empty($problemId)) {
            $this->renderError();
        }

        // 校验权限
        $problemInfo = OjProblemInterface::getDetail(array(
            'remote'        => StatusVars::REMOTE_HQU,
            'problem_id'    => $problemId,
        ));
        if (empty($problemInfo)) {
            $this->renderError('题目不存在！');
        }

        $this->renderIframe(array(
            'problemInfo'   => $problemInfo,
        ), 'problem/iframe/show_audit_history.php');
    }


    public function ajaxShowAction() {

        $globalId = (int) Request::getPOST('global-id');
        $problemInfo = OjProblemInterface::getById(array('id' => $globalId));
        if (empty($problemInfo)) {
            $this->renderError('题目不存在！');
        }
        if (! $problemInfo['hidden']) {
            $this->renderError('题目已经显示！');
        }
        OjProblemInterface::show(array('id' => $globalId));
        $this->setNotice(FrameworkVars::NOTICE_SUCCESS, '显示成功！');
        $this->renderAjax(0);
    }

    public function ajaxHideAction() {

        $globalId = (int) Request::getPOST('global-id');
        $problemInfo = OjProblemInterface::getById(array('id' => $globalId));
        if (empty($problemInfo)) {
            $this->renderError('题目不存在！');
        }
        if ($problemInfo['hidden']) {
            $this->renderError('题目已经隐藏！');
        }
        OjProblemInterface::hide(array('id' => $globalId));
        $this->setNotice(FrameworkVars::NOTICE_SUCCESS, '隐藏成功！');
        $this->renderAjax(0);
    }

    public function iframeSetUserAction() {

        // 获取参数
        $globalId = (int) Request::getGET('global-id');
        if (empty($globalId)) {
            $this->renderError('参数错误！');
        }
        $this->renderIframe(array(), 'problem/iframe/set_user.php');
    }

    public function ajaxSetUserAction() {

        // 获取参数
        $globalId = Request::getPOST('global-id');
        $username = trim(Request::getPOST('username', ''));

        // 校验用户
        $userInfo = UserCommonInterface::getByLoginName(array('login_name' => $username));
        if (empty($userInfo)) {
            $this->renderError('用户不存在！');
        }

        // 校验problem
        $problemInfo = OjProblemInterface::getById(array('id' => $globalId));
        if (empty($problemInfo)) {
            $this->renderError('题目不存在！');
        }

        OjProblemInterface::setUser(array(
            'id'       => $globalId,
            'username' => $username,
        ));
        $this->setNotice(FrameworkVars::NOTICE_SUCCESS, '设置成功！');
        $this->renderAjax(0);
    }


}
