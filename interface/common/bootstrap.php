<?php

if (!defined('INCLUDE_PATH')) {
    define('INCLUDE_PATH', __DIR__ . '/include');
}

// 载入library
require_once __DIR__ . '/../../library/bootstrap.php';

// 载入局部接口
require_once __DIR__ . '/../root/bootstrap.php';
require_once __DIR__ . '/../uc/bootstrap.php';

AutoLoader::setAutoDir(__DIR__ . '/helper');
AutoLoader::setAutoDir(__DIR__ . '/interface');
