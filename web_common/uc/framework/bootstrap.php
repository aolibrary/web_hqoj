<?php

// 定义PROJECT_PATH
define('PROJECT_PATH', dirname(__DIR__));

// 载入接口
require_once __DIR__ . '/../../../interface/common/bootstrap.php';
require_once __DIR__ . '/../../../interface/uc/bootstrap.php';

// 载入控制器
require_once __DIR__ . '/../../../framework/bootstrap.php';

// 载入项目公共文件
require_once PROJECT_PATH . '/framework/ProjectController.class.php';
