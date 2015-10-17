<?php

// 定义PROJECT_PATH
define('PROJECT_PATH', dirname(__DIR__));

// 载入模型
require_once __DIR__ . '/../../../interface/common/bootstrap.php';

// 载入控制器
require_once __DIR__ . '/../../../framework/backend/bootstrap.php';

// 载入项目公共文件
require_once PROJECT_PATH . '/framework/ProjectController.class.php';
