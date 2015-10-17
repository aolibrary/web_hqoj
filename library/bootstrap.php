<?php

if (!defined('PLUGIN_PATH')) {
    define('PLUGIN_PATH', __DIR__ . '/plugin');
}
if (!defined('DATA_PATH')) {
    define('DATA_PATH', dirname(__DIR__) . '/data');
}

// 自动加载配置
require_once __DIR__ . '/util/autoload/AutoLoader.class.php';
spl_autoload_register(array('AutoLoader', 'autoLoad'));
AutoLoader::setAutoDir(__DIR__ . '/api/interface');
AutoLoader::setAutoDir(__DIR__ . '/api/model_vars');
AutoLoader::setAutoDir(__DIR__ . '/config');
AutoLoader::setAutoDir(__DIR__ . '/debug');
AutoLoader::setAutoDir(__DIR__ . '/util');
AutoLoader::setAutoDir(__DIR__ . '/vars');

// 自动加载module下各个子模块的bootstrap.php
$dh = opendir(__DIR__ . '/module');
while (false !== ($file = readdir($dh))) {
    if ($file == '.' || $file == '..') {
        continue;
    }
    $bootstrapFile = __DIR__ . '/module/' . $file . '/bootstrap.php';
    require_once $bootstrapFile;
}

// 是否启用ErrorHandle记录错误，还是直接输出到页面或者控制台；
if (GlobalConfig::$ERROR_HANDLER_ENABLE) {
    register_shutdown_function(array( 'ErrorHandler', 'logParseError' ));
    set_error_handler(array( 'ErrorHandler', 'logError' ), error_reporting());
    set_exception_handler(array( 'ErrorHandler', 'logException' ));
}
