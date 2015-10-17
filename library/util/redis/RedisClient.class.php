<?php

/**
 * Class RedisClient    单例模式，防止建立多余的连接
 */

class RedisClient {

    // 缓存对象
    private static $instanceList = array();

    // 缓存redis对象
    private static $redisHandleList = array();

    /**
     * @param   $config
     * @return  redis
     */
    public static function getInstance($config) {
        $key = md5(serialize($config));
        if (!array_key_exists($key, self::$instanceList)) {
            self::$instanceList[$key] = new self($config);
        }
        return self::$instanceList[$key];
    }

    private $config = array();

    private function __construct($config) {

        $this->config = $config;
        // 缓存主redis
        $server =  $this->config['master'];
        $key = $this->getHandleKey($server);
        if (!array_key_exists($key, self::$redisHandleList)) {
            $redis = new Redis();
            if ($redis->connect($server['host'], $server['port'], $server['timeout'])) {
                $redis->select($server['db']);
                self::$redisHandleList[$key] = $redis;
            } else {
                throw new LibraryException("主Redis（IP：{$server['host']}:{$server['port']}）连接失败！");
            }
        }
        // 从slaves中随机获取一台可用的从服务器
        $slaves = $this->config['slaves'];
        $n = count($slaves);
        while ($n > 0) {
            $i = sprintf('%u', crc32(uniqid('', true)))%$n;
            $server = $slaves[$i];
            $key = $this->getHandleKey($server);
            if (array_key_exists($key, self::$redisHandleList)) {
                $this->config['slave'] = $server;
                break;
            } else {
                $redis = new Redis();
                if ($redis->connect($server['host'], $server['port'], $server['timeout'])) {
                    $redis->select($server['db']);
                    self::$redisHandleList[$key] = $redis;
                    $this->config['slave'] = $server;
                    break;
                } else {
                    trigger_error("从Redis（IP：{$server['host']}:{$server['port']}）连接失败", E_USER_WARNING);
                    // 连接失败的话，忽略该台从服务器
                    unset($slaves[$i]);
                    $slaves = array_values($slaves);
                    $n --;
                }
            }
        }
        if (empty($this->config['slave'])) {
            throw new LibraryException('从Redis全部连接失败');
        }
    }

    private function __clone() {}

    private function getHandleKey($server) {

        $server = Arr::filter($server, array(
            'host',
            'port',
            'db',
        ));
        ksort($server);
        return md5(implode('_', $server));
    }

    /**
     * 获取主服务器
     *
     * @return  redis
     */
    public function getMasterRedis() {
        $key = $this->getHandleKey($this->config['master']);
        return self::$redisHandleList[$key];
    }

    /**
     * 获取从服务器
     *
     * @return  redis
     */
    public function getSlaveRedis() {
        $key = $this->getHandleKey($this->config['slave']);
        return self::$redisHandleList[$key];
    }

    private static $readonlyFunc = array(
        'get', 'getbit', 'getrange', 'mget', 'getmultiple', 'strlen',
        'dump', 'exists', 'keys', 'getkeys', 'scan', 'randomkey', 'type', 'ttl', 'pttl',
        'hexists', 'hget', 'hgetall', 'hkeys', 'hlen', 'hmget', 'hvals', 'hscan',
        'lindex', 'lget', 'llen', 'lsize', 'lrange', 'lgetrange',
        'scard', 'ssize', 'sdiff', 'sinter', 'sismember', 'scontains', 'smembers', 'sgetmembers',
        'zcard', 'zsize', 'zcount', 'zrange', 'zrangebyscore', 'zrevrangebyscore', 'zrangebylex', 'zrank', 'zrevrank', 'zrevrange', 'zscore', 'zscan',
    );

    public function __call($func, $args) {

        $redis = in_array(strtolower($func), self::$readonlyFunc) ? $this->getSlaveRedis() : $this->getMasterRedis();
        if (!method_exists($redis, $func)) {
            throw new LibraryException("redis中不存在方法：{$func}");
        }
        return call_user_func_array(array($redis, $func), $args);
    }

}
