<?php

class EditController extends ProjectController {

    public function defaultAction() {

        $docId = (int) Request::getGET('doc-id');
        $docInfo = DocInterface::getById(array('id' => $docId));
        if (empty($docInfo)) {
            $this->renderError('文章不存在！');
        }

        $this->renderFramework(array(
            'docInfo'   => $docInfo,
        ), 'doc/edit.php');
    }

    public function ajaxSubmitAction() {

        // 获取参数
        $docId   = (int) Request::getPOST('doc-id');
        $title   = trim(Request::getPOST('title'));
        $content = Request::getPOST('content', '', true);

        if (strlen($content) > 65535) {
            $this->renderError('文章限制为64KB！');
        }

        // 校验
        if (empty($title) || mb_strlen($title, 'utf8') > 50) {
            $this->renderError('Title限制1-50个字！');
        }

        // 校验doc
        $docInfo = DocInterface::getById(array('id' => $docId));
        if (empty($docInfo)) {
            $this->renderError('文章不存在！');
        }

        // 更新
        $data = array(
            'id'      => $docId,
            'title'   => $title,
            'content' => $content,
        );
        DocInterface::save($data);
        $this->setNotice(FrameworkVars::NOTICE_SUCCESS, '编辑成功！');
        $this->renderAjax(0);
    }

    public function iframeChangeAction() {

        $docId   = (int) Request::getGET('doc-id');
        $docInfo = DocInterface::getById(array('id' => $docId));
        if (empty($docInfo)) {
            $this->renderError('文章不存在！');
        }

        $userInfo = UserCommonInterface::getById(array('id' => $docInfo['user_id']));
        $this->renderIframe(array(
            'userInfo'  => $userInfo,
            'docInfo'   => $docInfo,
        ), 'doc/iframe/change.php');
    }

    public function ajaxChangeAction() {

        // 获取参数
        $docId    = (int) Request::getPOST('doc-id');
        $username = trim(Request::getPOST('username', ''));
        $category = (int) Request::getPOST('category');

        if (empty($username) || !array_key_exists($category, DocVars::$CATEGORY)) {
            $this->renderError('参数错误！');
        }

        // 校验用户
        $userInfo = UserCommonInterface::getByLoginName(array('login_name' => $username));
        if (empty($userInfo)) {
            $this->renderError('用户不存在！');
        }

        // 校验doc
        $docInfo = DocInterface::getById(array('id' => $docId));
        if (empty($docInfo)) {
            $this->renderError('文章不存在！');
        }

        // 更新
        DocInterface::change(array(
            'id'        => $docId,
            'username'  => $username,
            'category'  => $category,
        ));

        $this->setNotice(FrameworkVars::NOTICE_SUCCESS, '操作成功');
        $this->renderAjax(0);
    }

}
