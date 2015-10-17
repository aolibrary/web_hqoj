<?php

class ListController extends ProjectController {

    public function defaultAction() {

        $pageSize = 50;

        $page       = Pager::get();
        $category   = (int) Request::getGET('category', -1);
        $title      = trim(Request::getGET('title', ''));
        $username   = trim(Request::getGET('username', ''));

        $where = array();
        if ($category != -1) {
            $where[] = array('category', '=', $category);
        }
        if (!empty($username)) {
            $userInfo = UserCommonInterface::getByLoginName(array('login_name' => $username));
            $where[] = array('user_id', '=', Arr::get('id', $userInfo, 0));
        }
        if (!empty($title)) {
            $where[] = array('title', 'LIKE', "%{$title}%");
        }

        if ($category != -1) {
            $order = array(
                'hidden'    => 'ASC',
                'title'     => 'ASC',
                'id'        => 'DESC',
            );
        } else {
            $order = array(
                'id'    => 'DESC',
            );
        }

        $offset     = ($page-1)*$pageSize;
        $docList    = DocInterface::getList(array(
            'where'  => $where,
            'order'  => $order,
            'limit'  => $pageSize,
            'offset' => $offset,
        ));
        $allCount = empty($docList) ? 0 : DocInterface::getCount($where);

        $userIds = array_unique(array_column($docList, 'user_id'));
        $userHash = UserCommonInterface::getById(array('id' => $userIds));

        // 缓存部分的html
        $html = array();
        $html['pager'] = $this->view->fetch(array(
            'renderAllCount' => $allCount,
            'renderPageSize' => $pageSize,
            'renderRadius'   => 7,
        ), 'widget/pager.php');

        $this->renderFramework(array(
            'html'      => $html,
            'docList'   => $docList,
            'userHash'  => $userHash,
        ), 'doc/list.php');
    }

    public function ajaxShowAction() {

        $docId = (int) Request::getPOST('doc-id');
        $docInfo = DocInterface::getById(array('id' => $docId));
        if (empty($docInfo)) {
            $this->renderError('文章不存在！');
        }
        if (! $docInfo['hidden']) {
            $this->renderError('文章已经显示！');
        }

        if (empty($docInfo['title']) || empty($docInfo['content'])) {
            $this->renderError('文章标题为空，或者文章内容为空，无法显示！');
        }

        DocInterface::show(array('id' => $docId));
        $this->setNotice(FrameworkVars::NOTICE_SUCCESS, '操作成功');
        $this->renderAjax(0);
    }

    public function ajaxHideAction() {

        $docId = (int) Request::getPOST('doc-id');
        $docInfo = DocInterface::getById(array('id' => $docId));
        if (empty($docInfo)) {
            $this->renderError('文章不存在！');
        }
        if ($docInfo['hidden']) {
            $this->renderError('文章已经隐藏！');
        }

        DocInterface::hide(array('id' => $docId));
        $this->setNotice(FrameworkVars::NOTICE_SUCCESS, '操作成功');
        $this->renderAjax(0);
    }
}
