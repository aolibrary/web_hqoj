<?php

class RedisVars {

    public static $friendTypes = array(
        Redis::REDIS_STRING     => 'string',
        Redis::REDIS_SET        => 'set',
        Redis::REDIS_LIST       => 'list',
        Redis::REDIS_ZSET       => 'zset',
        Redis::REDIS_HASH       => 'hash',
        Redis::REDIS_NOT_FOUND  => 'other',
    );

}