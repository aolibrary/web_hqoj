<?php

// 定义PROJECT_PATH
define('PROJECT_PATH', dirname(__DIR__));

// 载入控制器
require_once __DIR__ . '/../../../framework/backend/bootstrap.php';

// 载入相关接口
require_once __DIR__ . '/../../../interface/doc/bootstrap.php';

// 载入项目公共文件
require_once __DIR__ . '/ProjectController.class.php';