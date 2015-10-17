<?php

class MemcachedConfig {

    // alias
    public static $SERVER_COMMON;

    // 兼容旧接口
    public static function init() {

        self::$SERVER_COMMON = array( self::$MS_1, self::$MS_2 );
    }

    // server list
    private static $MS_1 = array(
        'host'      => '127.0.0.1',
        'port'      => 11211,
        'weight'    => 1,
    );

    private static $MS_2 = array(
        'host'      => '127.0.0.1',
        'port'      => 11212,
        'weight'    => 1,
    );

}

MemcachedConfig::init();