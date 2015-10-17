<?php

class LoggerKeys {

    public static $allTags = array(

        // php错误
        'handle',         // ErrorHandle系统捕获到的异常和错误
        'library',        // library中主动记录的异常和错误
        'framework',      // framework中主动记录的异常和错误
        'interface',      // interface接口，公共接口错误
        'controller',     // project中发生的错误
        'mysql',          // mysql连接或者query时发生的错误
        'judge',          // judge相关
    );

    public static $phpErrors = array(
        'handle',
        'library',
        'framework',
        'interface',
        'controller',
        'mysql',
    );

}