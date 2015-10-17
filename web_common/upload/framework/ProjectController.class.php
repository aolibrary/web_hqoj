<?php

abstract class ProjectController extends BaseController {

    public function __construct() {

        parent::__construct();

        // 必须登录
        if (empty($this->loginUserInfo)) {
            $this->login();
        }
    }
}