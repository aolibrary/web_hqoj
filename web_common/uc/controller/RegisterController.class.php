<?php

class RegisterController extends ProjectController {

    public function defaultAction() {

        $this->render(array(), 'register.php');
    }

    public function ajaxCheckUserAction() {

        $username = Request::getPOST('username');

        if (!Regex::match($username, RegexVars::USERNAME)) {
            $this->renderAjax(1, '用户名格式不正确！', array('message' => '用户名格式不正确！'));
        }

        // 过滤用户名
        if (false !== strpos(strtolower($username), 'admin')) {
            $this->renderAjax(1, '用户已经存在！', array('message' => '用户已经存在！'));
        }

        // 用户名是否被使用
        $userInfo = UcUserInterface::getByLoginName(array('login_name' => $username));
        if (!empty($userInfo)) {
            $this->renderAjax(1, "用户 {$username} 已经存在！", array('message' => "用户 {$username} 已经存在！"));
        }
        $this->renderAjax(0, '恭喜你，用户名没有被占用！', array('valid' => 1));
    }

    public function ajaxSubmitAction() {

        $username = Request::getPOST('username');
        $password = Request::getPOST('password');
        $verify   = Request::getPOST('verify');

        if (!Regex::match($username, RegexVars::USERNAME)) {
            $this->renderAjax(1, '用户名格式不正确！');
        }

        // 校验密码格式
        if (!Regex::match($password, RegexVars::PASSWORD)) {
            $this->renderAjax(1, '密码长度为6-20位！');
        }

        // 校验验证码
        $code = Session::get('check_code');
        if (strtolower($verify) != $code) {
            $this->renderAjax(1, '验证码错误，请重试！');
        }

        // 过滤用户名
        if (false !== strpos(strtolower($username), 'admin')) {
            $this->renderAjax(1, '用户已经存在！');
        }

        // 校验用户是否存在
        $userInfo = UcUserInterface::getByLoginName(array('login_name' => $username));
        if (!empty($userInfo)) {
            $this->renderAjax(1, '用户名已经被占用！');
        }

        // 保存
        $data = array(
            'username'  => $username,
            'password'  => $password,
            'reg_ip'    => Http::getClientIp(),
        );
        UcUserInterface::save($data);
        $this->renderAjax(0);
    }
}
