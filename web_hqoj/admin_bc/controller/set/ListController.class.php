<?php

class ListController extends ProjectController {

    public function defaultAction() {

        $pageSize = 50;

        // 获取参数
        $page     = Pager::get();
        $title    = Request::getGET('title');
        $status   = (int) Request::getGET('status');
        $username = Request::getGET('username');

        // userInfo
        $userInfo = array();
        if (!empty($username)) {
            $userInfo = UserCommonInterface::getByLoginName(array('login_name' => $username));
        }

        // 构建where
        if (!empty($username) && empty($userInfo)) {
            $where = false;
        } else {
            $where = array();
            if (!empty($userInfo)) {
                $where[] = array('user_id', '=', $userInfo['id']);
            }
            if (!empty($status) && $status != -1) {
                $where[] = array('hidden', '=', $status - 1);
            }
            if (!empty($title)) {
                $where[] = array('title', 'LIKE', "%{$title}%");
            }
        }
        $order = array(
            'listing_status'    => 'DESC',
            'refresh_at'        => 'DESC',
            'id'                => 'DESC',
        );
        $offset = ($page-1)*$pageSize;
        $setList  = OjProblemSetInterface::getList(array(
            'where'  => $where,
            'order'  => $order,
            'limit'  => $pageSize,
            'offset' => $offset,
        ));
        $allCount = 0;
        if (!empty($setList)) {
            $allCount = OjProblemSetInterface::getCount($where);
        }

        foreach ($setList as &$setInfo) {
            $globalIds = (array) json_decode($setInfo['problem_set'], true);
            $setInfo['count'] = count($globalIds);
        }

        // 获取用户
        $userIds = array_unique(array_column($setList, 'user_id'));
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
            'html'     => $html,
            'setList'  => $setList,
            'userHash' => $userHash,
        ), 'set/list.php');
    }

    public function ajaxStickAction() {

        $setId = Request::getPOST('set-id');
        $setInfo = OjProblemSetInterface::getById(array('id' => $setId));
        if (empty($setInfo)) {
            $this->renderError('专题不存在！');
        }
        if ($setInfo['listing_status']) {
            $this->renderError('已经置顶！');
        }
        OjProblemSetInterface::stick(array('id' => $setId));
        $this->setNotice(FrameworkVars::NOTICE_SUCCESS, '操作成功！');
        $this->renderAjax(0);
    }

    public function ajaxStickCancelAction() {

        $setId = Request::getPOST('set-id');
        $setInfo = OjProblemSetInterface::getById(array('id' => $setId));
        if (empty($setInfo)) {
            $this->renderError('专题不存在！');
        }
        if (!$setInfo['listing_status']) {
            $this->renderError('已取消置顶！');
        }
        OjProblemSetInterface::cancelStick(array('id' => $setId));
        $this->setNotice(FrameworkVars::NOTICE_SUCCESS, '操作成功！');
        $this->renderAjax(0);
    }
}
