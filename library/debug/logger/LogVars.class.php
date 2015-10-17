<?php

class LogVars {

    public static $friendlyErrorType = array(
        E_ERROR             => 'E_ERROR',               // 1
        E_WARNING           => 'E_WARNING',             // 2
        E_PARSE             => 'E_PARSE',               // 4
        E_NOTICE            => 'E_NOTICE',              // 8
        E_CORE_ERROR        => 'E_CORE_ERROR',          // 16
        E_CORE_WARNING      => 'E_CORE_WARNING',        // 32
        E_COMPILE_ERROR     => 'E_COMPILE_ERROR',       // 64
        E_COMPILE_WARNING   => 'E_COMPILE_WARNING',     // 128
        E_USER_ERROR        => 'E_USER_ERROR',          // 256
        E_USER_WARNING      => 'E_USER_WARNING',        // 512
        E_USER_NOTICE       => 'E_USER_NOTICE',         // 1024
        E_STRICT            => 'E_STRICT',              // 2048
        E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',   // 4096
        E_DEPRECATED        => 'E_DEPRECATED',          // 8192
        E_USER_DEPRECATED   => 'E_USER_DEPRECATED',     // 16384
        E_ALL               => 'E_ALL',                 // 30719
    );

    // 可以忽略的错误类型
    public static $noticeType = array(
        E_NOTICE,
        E_USER_NOTICE,
    );

    // 非致命错误
    public static $warnType   = array(
        E_WARNING,
        E_CORE_WARNING,
        E_COMPILE_WARNING,
        E_USER_WARNING,
        E_STRICT,
        E_DEPRECATED,
        E_USER_DEPRECATED,
    );

    // 致命错误
    public static $errorType  = array(
        E_ERROR,
        E_PARSE,
        E_CORE_ERROR,
        E_COMPILE_ERROR,
        E_USER_ERROR,
        E_RECOVERABLE_ERROR,
    );

    // 错误级别
    const LEVEL_FATAL   = 1;    // 发生原因未知的(致命)错误，无法控制的错误，极端错误，难以解决
    const LEVEL_ERROR   = 2;    // 致命错误，可以通过代码修改解决
    const LEVEL_WARN    = 3;    // 警告
    const LEVEL_NOTICE  = 4;    // 注意
    const LEVEL_INFO    = 5;    // 记录信息
    const LEVEL_DEBUG   = 6;    // 调试代码
    const LEVEL_TRACE   = 7;    // 跟踪，一般不用

    // 文案
    public static $levelText = array(
        self::LEVEL_FATAL   => 'Fatal',
        self::LEVEL_ERROR   => 'Error',
        self::LEVEL_WARN    => 'Warn',
        self::LEVEL_NOTICE  => 'Notice',
        self::LEVEL_INFO    => 'Info',
        self::LEVEL_DEBUG   => 'Debug',
        self::LEVEL_TRACE   => 'Trace',
    );

    // level颜色
    public static $levelColor = array(
        self::LEVEL_FATAL   => 'red',
        self::LEVEL_ERROR   => 'red',
        self::LEVEL_WARN    => 'orange',
        self::LEVEL_NOTICE  => 'green',
        self::LEVEL_INFO    => 'green',
        self::LEVEL_DEBUG   => 'green',
        self::LEVEL_TRACE   => 'green',
    );

}
