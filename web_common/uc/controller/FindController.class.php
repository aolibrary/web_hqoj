<?php

class FindController extends ProjectController {

    public function defaultAction() {

        $this->render(array(), 'find.php');
    }

    public function ajaxSendEmailAction() {

        $email  = Request::getPOST('email');
        $verify = Request::getPOST('verify');

        if (empty($verify)) {
            $this->renderAjax(1, '请输入图片验证码！');
        }

        if (empty($email)) {
            $this->renderAjax(1, '请填写邮箱！');
        }

        // 校验验证码
        $imgCode = Session::get('check_code');
        if (strtolower($verify) != $imgCode) {
            $this->renderAjax(2, '图片验证码错误！');
        }

        if (!Regex::match($email, RegexVars::EMAIL)) {
            $this->renderAjax(1, '邮箱格式错误！');
        }

        // 是否存在
        $userInfo = UcUserInterface::getByLoginName(array('login_name' => $email));
        if (empty($userInfo)) {
            $this->renderAjax(1, '用户邮箱不存在！');
        }

        $code = UcAuthInterface::sendEmailCode(array(
            'email'     => $email,
            'repeat_at' => time()+60,
        ));
        if (false === $code) {
            $this->renderAjax(1, '服务器繁忙，请1分钟后重试！');
        }
        $this->renderAjax(0);
    }

    public function ajaxSubmitAction() {

        $email     = Request::getPOST('email');
        $checkCode = Request::getPOST('check-code');
        $verify    = Request::getPOST('verify');

        if (empty($email) || empty($verify) || empty($checkCode)) {
            $this->renderAjax(1, '请填写信息！');
        }

        // 校验验证码
        $imgCode = Session::get('check_code');
        if (strtolower($verify) != $imgCode) {
            $this->renderAjax(2, '图片验证码错误！');
        }

        $check = UcAuthInterface::checkEmailCode(array(
            'email' => $email,
            'code'  => $checkCode,
        ));
        if (false === $check) {
            $this->renderAjax(1, '邮箱验证码错误！');
        }

        // 删除email code
        UcAuthInterface::deleteEmailCode(array('email' => $email));

        // reset ticket
        $resetTicket = UcAuthInterface::makeResetTicket(array('login_name' => $email));
        $this->renderAjax(0, 'Success', array( 'resetTicket' => $resetTicket ));
    }

}