<?php

class MemcachedPool {

    // 缓存memcached对象
    private static $memcachedList = array();

    /**
     * 实现单例模式
     *
     * @param   array   $config
     * @return  Memcached
     */
    public static function getMemcached($config) {

        $key = md5(serialize($config));
        if (array_key_exists($key, self::$memcachedList)) {
            return self::$memcachedList[$key];
        }

        $handle = new Memcached();
        foreach ($config as $serverInfo) {
            $handle->addServer($serverInfo['host'], $serverInfo['port'], $serverInfo['weight']);
        }

        self::$memcachedList[$key] = $handle;
        return $handle;
    }

}