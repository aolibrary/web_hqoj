<?php

/**
 * Class GlobalConfig 全局配置
 */

class GlobalConfig {

    // 全站统一编码
    const CONTENT_CHARSET   = 'UTF-8';

    // 需要处理的错误类型
    public static function initErrorReporting() {
        error_reporting(E_ALL);
    }

    // 如果启用，框架会自动处理错误和异常，处理方式在ErrorHandler中
    public static $ERROR_HANDLER_ENABLE = false;

    // 是否启用Logger，如果为false，那么Logger会直接return
    public static $LOGGER_ENABLE    = true;

    // Logger是否用异步记录，需要Gearman扩展支持
    public static $LOGGER_ASYNC     = true;

    // Debug工具是否要开启
    public static $DEBUG_ENABLE     = true;

}
