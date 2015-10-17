<?php

if (!defined('PROJECT_PATH')) {
    throw new FrameworkException('PROJECT_PATH没有定义！');
}

// 载入公共接口
require_once __DIR__ . '/../interface/common/bootstrap.php';

// 载入framework
require_once __DIR__ . '/BaseController.class.php';
require_once __DIR__ . '/View.class.php';
require_once __DIR__ . '/Router.class.php';
require_once __DIR__ . '/FrameworkVars.class.php';
