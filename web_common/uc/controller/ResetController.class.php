<?php

class ResetController extends ProjectController {

    public function defaultAction() {

        $resetTicket = Request::getGET('reset-ticket');

        if (empty($resetTicket)) {
            $this->renderError();
        }

        $userInfo = UcAuthInterface::getUserInfoByResetTicket(array('reset_ticket' => $resetTicket));
        if (empty($userInfo)) {
            $this->renderError('你访问的页面已经过期，请重新操作！');
        }

        $this->renderFramework(array(
            'userInfo' => $userInfo,
        ), 'reset.php');
    }

    public function ajaxSubmitAction() {

        $resetTicket = Request::getPOST('reset-ticket');
        $password    = Request::getPOST('password');

        if (empty($password) || empty($resetTicket)) {
            $this->renderAjax(1, '参数错误！');
        }
        if (strlen($password) < 6 || strlen($password) > 30) {
            $this->renderAjax(1, '密码长度为6-30位！');
        }

        $userInfo = UcAuthInterface::getUserInfoByResetTicket(array('reset_ticket' => $resetTicket));
        if (empty($userInfo)) {
            $this->renderAjax(1, '你的操作已经过期，请重新操作！');
        }

        // 修改密码
        UcUserInterface::save(array(
            'id'        => $userInfo['id'],
            'password'  => $password,
        ));

        // 删除reset ticket
        UcAuthInterface::deleteResetTicket(array('reset_ticket' => $resetTicket));
        UcUserInterface::logout();
        $this->renderAjax(0);
    }
}
