<?php

class LoginController extends ProjectController {

    public function defaultAction() {

        $this->render(array(), 'login.php');
    }

    public function ajaxLoginAction() {

        $loginName = Request::getPOST('login-name');
        $password = Request::getPOST('password');
        $backUrl  = Request::getPOST('back-url', '//www.hqoj.net/');

        if (empty($loginName) || empty($password)) {
            $this->renderAjax(1, '请填写信息！');
        }

        $retInfo = UcUserInterface::login(array(
            'login_name'    => $loginName,
            'password'      => $password,
        ));
        $ret = $retInfo['ret'];

        if ($ret === false) {
            $this->renderAjax(1, $retInfo['msg']);
        }
        $this->renderAjax(0, '登陆成功！', array('backUrl' => $backUrl));
    }

    public function ajaxCheckAction() {

        $data = Request::getPOST('test', 0);
        $this->renderAjax(0, '', array('test' => $data));
    }

}