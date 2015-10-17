<?php

abstract class ProjectController extends BackendController {

    public function __construct() {

        parent::__construct();

        if (!in_array($this->loginUserInfo['username'], array(
            'aozhongxu',
        ))) {
            $this->renderError('你没有权限访问！');
        }
    }

}