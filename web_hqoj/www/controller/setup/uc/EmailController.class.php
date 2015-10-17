<?php

class EmailController extends ProjectBackendController {

    public function defaultAction() {

        $this->renderFramework(array(), 'setup/uc/email.php');
    }

    public function ajaxSendEmailAction() {

        $email  = Request::getPOST('email');

        if (empty($email)) {
            $this->renderError('请填写邮箱！');
        }

        if (!Regex::match($email, RegexVars::EMAIL)) {
            $this->renderError('邮箱格式错误！');
        }

        // 是否已经被绑定
        $userInfo = UserCommonInterface::getByLoginName(array('login_name' => $email));
        if (!empty($userInfo)) {
            $this->renderError('该邮箱已经被绑定！');
        }

        $code = AuthCommonInterface::sendEmailCode(array(
            'email'     => $email,
            'repeat_at' => time()+60,
        ));
        if (false === $code) {
            $this->renderError('服务器繁忙，请1分钟后重试！');
        }
        $this->renderAjax(0);
    }

    public function ajaxSubmitAction() {

        $email     = Request::getPOST('email');
        $checkCode = Request::getPOST('check-code');

        if (empty($email) || empty($checkCode)) {
            $this->renderError('请填写信息！');
        }

        // 是否已经被绑定
        $userInfo = UserCommonInterface::getByLoginName(array('login_name' => $email));
        if (!empty($userInfo)) {
            $this->renderError('该邮箱已经被绑定！');
        }

        $check = AuthCommonInterface::checkEmailCode(array(
            'email' => $email,
            'code'  => $checkCode,
        ));
        if (false === $check) {
            $this->renderError('邮箱验证码错误！');
        }

        // 删除email code
        AuthCommonInterface::deleteEmailCode(array('email' => $email));

        // 修改用户
        UserCommonInterface::save(array(
            'id'    => $this->loginUserInfo['id'],
            'email' => $email,
        ));

        $this->setNotice(FrameworkVars::NOTICE_SUCCESS, '绑定邮箱成功！');
        $this->renderAjax(0);
    }
}