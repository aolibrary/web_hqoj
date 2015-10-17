<?php

abstract class ProjectController extends BaseController {

    protected $isOjAdmin = false;

    public function __construct() {

        parent::__construct();

        $this->checkLogin();

        if (!empty($this->loginUserInfo) && RootCommonInterface::allowed(array(
            'user_id'   => $this->loginUserInfo['id'],
            'path'      => '/hqoj/admin',
        ))) {
            $this->isOjAdmin = true;
        }

        $this->view->assign(array(
            'isOjAdmin' => $this->isOjAdmin,
        ));
    }

    protected function checkLogin() {

        if (!empty($this->loginUserInfo)) {
            return;
        }

        $controller = Router::$CONTROLLER;
        if (0 === strpos($controller, 'setup_')
        || $controller == 'problem_submit') {
            $this->login();
        }

    }

    protected function renderFramework($params, $tpl) {
        $content = $this->view->fetch($params, $tpl);
        parent::renderFramework(array(
            'frameworkContent'  => $content,
        ), __DIR__ . '/template/framework.php');
    }

}