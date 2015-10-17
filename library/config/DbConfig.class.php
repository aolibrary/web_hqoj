<?php

class DbConfig {

    // 全站统一数据库编码
    const DEFAULT_CHARSET   = 'utf8mb4';

    // 事务服务器
    public static $SERVER_TRANS = array(
        'host'     => '127.0.0.1',
        'username' => 'root',
        'password' => '123',
        'port'     => 3307,
    );

    // 主服务器
    public static $SERVER_MASTER = array(
        'host'     => '127.0.0.1',
        'username' => 'root',
        'password' => '123',
        'port'     => 3307,
    );

    // 从服务器
    public static $SERVER_SLAVE = array(
        'host'     => '127.0.0.1',
        'username' => 'root',
        'password' => '123',
        'port'     => 3307,
    );

    // 数据库枚举值
    const DB_COMMON     = '';
    const DB_LOG        = 'hqoj';
    const DB_HQOJ       = 'hqoj';

}