<?php

class PasswordController extends ProjectBackendController {

    public function defaultAction() {

        $this->renderFramework(array(), 'setup/uc/password.php');
    }

    public function ajaxUpdatePasswordAction() {

        // 获取参数
        $oldPassword = Request::getPOST('old-password');
        $password    = trim(Request::getPOST('password'));

        if (!Regex::match($password, RegexVars::PASSWORD)) {
            $this->renderError('新密码限制为6-20位！');
        }

        $encryptPassword = UserCommonInterface::encryptPassword(array('password' => $oldPassword));
        if ($encryptPassword != $this->loginUserInfo['password']) {
            $this->renderError('旧密码不正确！');
        }

        $data = array(
            'id'        => $this->loginUserInfo['id'],
            'password'  => $password,
        );
        UserCommonInterface::save($data);
        UserCommonInterface::logout();
        $this->renderAjax(0);
    }
}
