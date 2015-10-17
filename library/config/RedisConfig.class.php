<?php

class RedisConfig {

    // alias
    public static $SERVER_TEST;             // 用于测试
    public static $SERVER_COMMON;           // 公共
    public static $SERVER_AX_TEST;          // 安心客测试

    // 兼容旧接口
    public static function init() {

        self::$SERVER_TEST = array(
            'master'    => self::$MS_1,
            'slaves'    => array(self::$SL_1, self::$SL_2),
        );

        self::$SERVER_COMMON = array(
            'master'    => self::$MS_1,
            'slaves'    => array(self::$SL_1, self::$SL_2),
        );

        self::$SERVER_AX_TEST = array(
            'master'    => self::$MS_AX,
            'slaves'    => array(self::$MS_AX),
        );
    }

    // server list
    private static $MS_1 = array(
        'host' => '127.0.0.1',
        'port'     => 6379,
        'db'       => 0,
        'timeout'  => 0,
        'password' => '',   // 预留
        'weight'   => 1,    // 预留
    );

    private static $SL_1 = array(
        'host' => '127.0.0.1',
        'port'     => 6379,
        'db'       => 0,
        'timeout'  => 0,
        'password' => '',   // 预留
        'weight'   => 1,    // 预留
    );

    private static $SL_2 = array(
        'host' => '127.0.0.1',
        'port'     => 6379,
        'db'       => 0,
        'timeout'  => 0,
        'password' => '',   // 预留
        'weight'   => 2,    // 预留
    );

    private static $MS_AX = array(
        'host' => '192.168.2.2',
        'port'     => 6379,
        'db'       => 0,
        'timeout'  => 0,
        'password' => '',   // 预留
        'weight'   => 1,    // 预留
    );

}

RedisConfig::init();
