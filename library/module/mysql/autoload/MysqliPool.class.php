<?php

class MysqliPool {

    // mysqli
    private static $mysqliList = array();

    /**
     * @param   array   $server     DbConfig中配置
     * @param   bool    $temporary  是否是建立临时连接，如果是，那么新建，创建事务的时候需要使用临时连接
     * @return  mysqli
     */
    public static function getMysqli($server, $temporary = false) {

        $server = Arr::filter($server, array(
            'host',
            'username',
            'password',
            'port',
        ));

        if ($temporary) {
            $mysqli = self::createMysqli($server);
            self::$mysqliList[] = $mysqli;
            return $mysqli;
        }

        $key = "{$server['username']}@{$server['host']}:{$server['port']}";
        if (array_key_exists($key, self::$mysqliList)) {
            return self::$mysqliList[$key];
        }
        $mysqli = self::createMysqli($server);
        self::$mysqliList[$key] = $mysqli;
        return $mysqli;
    }

    private static function createMysqli($server) {

        // 创建mysqli对象
        $mysqli = @new \mysqli($server['host'], $server['username'], $server['password'], '', $server['port']);
        for ($i = 1; ($i < 3) && $mysqli->connect_error; $i++) {
            usleep(50000);
            $mysqli = @new \mysqli($server['host'], $server['username'], $server['password'], '', $server['port']);
        }

        // 连接失败，记录日志
        if ($mysqli->connect_error) {
            Logger::fatal('mysql', $mysqli->connect_error);
            throw new LibraryException($mysqli->connect_error);
        }
        return $mysqli;
    }
}