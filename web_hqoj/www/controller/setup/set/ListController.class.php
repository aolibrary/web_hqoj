<?php

class ListController extends ProjectBackendController {

    public function defaultAction() {

        $pageSize = 20;

        // 获取参数
        $page   = Pager::get();
        $title  = Request::getGET('title');
        $status = (int) Request::getGET('status');

        // 构建where
        $where = array();
        $where[] = array('user_id', '=', $this->loginUserInfo['id']);
        if (!empty($title)) {
            $where[] = array('title', 'LIKE', "%{$title}%");
        }
        if (!empty($status) && $status != -1) {
            $where[] = array('hidden', '=', $status - 1);
        }

        // 获取数据
        $order = array(
            'refresh_at'    => 'DESC',
            'id'            => 'DESC',
        );
        $offset = ($page-1)*$pageSize;
        $setList = OjProblemSetInterface::getList(array(
            'where'     => $where,
            'order'     => $order,
            'limit'     => $pageSize,
            'offset'    => $offset,
        ));
        $allCount = OjProblemSetInterface::getCount($where);

        foreach ($setList as &$setInfo) {
            $problemJson = $setInfo['problem_set'];
            $globalIds = (array) json_decode($problemJson, true);
            $setInfo['count'] = count($globalIds);
        }

        // 缓存部分的html
        $html = array();
        $html['pager'] = $this->view->fetch(array(
            'renderAllCount' => $allCount,
            'renderPageSize' => $pageSize,
            'renderRadius'   => 8,
        ), 'widget/pager.php');

        // 输出
        $this->renderFramework(array(
            'html'      => $html,
            'setList'   => $setList,
        ), 'setup/set/list.php');
    }

    public function ajaxShowAction() {

        // 获取参数
        $setId = Request::getPOST('set-id');
        if (empty($setId)) {
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

        // 不完整
        if (empty($setInfo['title'])) {
            $this->renderError('标题不能为空！');
        }
        if (empty($setInfo['refresh_at'])) {
            $this->renderError('请设置刷新时间！');
        }

        if ($setInfo['hidden'] == 0) {
            $this->renderError('已经显示！');
        }

        // 更新数据
        OjProblemSetInterface::show(array('id' => $setId));

        $this->setNotice(FrameworkVars::NOTICE_SUCCESS, '操作成功！');
        $this->renderAjax(0);
    }

    public function ajaxHideAction() {

        // 获取参数
        $setId = Request::getPOST('set-id');
        if (empty($setId)) {
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

        if ($setInfo['hidden']) {
            $this->renderError('已经隐藏！');
        }

        // 更新数据
        OjProblemSetInterface::hide(array('id' => $setId));

        $this->setNotice(FrameworkVars::NOTICE_SUCCESS, '操作成功！');
        $this->renderAjax(0);
    }
}
