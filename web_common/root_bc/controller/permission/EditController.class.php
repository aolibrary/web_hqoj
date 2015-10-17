<?php

class EditController extends ProjectController {

    public function iframeEditAction() {

        $id = Request::getGET('permission-id', 0);
        if (empty($id)) {
            $this->renderIframeError('缺少参数！');
        }

        $permissionInfo = RootPermissionInterface::getById(array('id' => $id));
        if (empty($permissionInfo)) {
            $this->renderIframeError('权限不存在！');
        }

        $this->renderIframe(array(
            'permissionInfo' => $permissionInfo,
        ), 'permission/iframe/permission_edit.php');
    }

    public function ajaxSubmitAction() {

        $id   = Request::getPOST('permission-id');
        $description = trim(Request::getPOST('description'));

        if (empty($id) || empty($description)) {
            $this->renderAjax(1, '参数不能为空！');
        }

        RootPermissionInterface::save(array(
            'id'            => $id,
            'description'   => $description,
        ));
        $this->setNotice(FrameworkVars::NOTICE_SUCCESS, '修改成功！');
        $this->renderAjax(0);
    }
}
