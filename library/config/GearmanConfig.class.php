<?php

class GearmanConfig {

    // alias
    public static $SERVER_COMMON;

    // 兼容旧接口
    public static function init() {

        self::$SERVER_COMMON = array( self::$MS_1, self::$MS_2 );
    }

    // server list
    private static $MS_1 = array(
        'host'      => '127.0.0.1',
        'port'      => 4730,
    );

    private static $MS_2 = array(
        'host'      => '127.0.0.1',
        'port'      => 4730,
    );

}

GearmanConfig::init();