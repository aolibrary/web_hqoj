<?php

class AddController extends ProjectController {

    public function iframeAddAction() {

        $this->renderIframe(array(

        ), 'manager/iframe/add_manager.php');
    }

    public function ajaxAddAction() {

        $loginName = Request::getPOST('login-name');

        if (empty($loginName)) {
            $this->renderAjax(1, '请填写登录名！');
        }

        // 校验一个用户
        $userInfo = UserCommonInterface::getByLoginName(array(
            'login_name'    => $loginName,
        ));
        if (empty($userInfo)) {
            $this->renderAjax(1, "用户 {$loginName} 不存在！");
        }

        // 是否已经添加
        $managerInfo = RootManagerInterface::getByField(array(
            'user_id'   => $userInfo['id'],
        ));
        if (!empty($managerInfo)) {
            $this->renderAjax(1, "用户 {$loginName} 已经是管理员！");
        }

        // 添加用户到管理员
        RootManagerInterface::save(array(
            'login_name'    => $loginName,
        ));
        $this->setNotice(FrameworkVars::NOTICE_SUCCESS, '添加成功！');
        $this->renderAjax(0);
    }
}
