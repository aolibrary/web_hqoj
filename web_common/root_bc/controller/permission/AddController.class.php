<?php

class AddController extends ProjectController {

    public function iframeAddAction() {

        $this->renderIframe(array(), 'permission/iframe/add_permission.php');
    }

    public function ajaxSubmitAction() {

        $code        = trim(Request::getPOST('code'));
        $description = trim(Request::getPOST('description'));

        // 校验
        if (empty($code) || empty($description)) {
            $this->renderAjax(1, '参数不能为空！');
        }

        $ret = RootPermissionInterface::isValidCode(array('code' => $code));
        if (false === $ret) {
            $this->renderAjax(1, '权限码不合法！');
        }

        $ret = RootPermissionInterface::testMakeCode(array('code' => $code));
        if (false === $ret) {
            $this->renderAjax(1, '权限已经存在，无法创建权限！');
        }

        // 保存
        RootPermissionInterface::save(array(
            'description' => $description,
            'code'        => $code,
        ));
        $this->setNotice(FrameworkVars::NOTICE_SUCCESS, '添加成功！');
        $this->renderAjax(0);
    }
}