<?php

class Logger {

    public static function fatal($tag, $msg) {
        self::insert($tag, LogVars::LEVEL_FATAL, $msg);
    }

    public static function error($tag, $msg) {
        self::insert($tag, LogVars::LEVEL_ERROR, $msg);
    }

    public static function warn($tag, $msg) {
        self::insert($tag, LogVars::LEVEL_WARN, $msg);
    }

    public static function notice($tag, $msg) {
        self::insert($tag, LogVars::LEVEL_NOTICE, $msg);
    }

    public static function info($tag, $msg) {
        self::insert($tag, LogVars::LEVEL_INFO, $msg);
    }

    public static function debug($tag, $msg) {
        self::insert($tag, LogVars::LEVEL_DEBUG, $msg);
    }

    public static function trace($tag, $msg) {
        self::insert($tag, LogVars::LEVEL_TRACE, $msg);
    }

    private static function insert($tag, $level, $msg) {

        // 是否需要Logger
        if (! GlobalConfig::$LOGGER_ENABLE) {
            return;
        }

        // 临时关闭Logger
        $tmpEnable = GlobalConfig::$LOGGER_ENABLE;
        GlobalConfig::$LOGGER_ENABLE = false;

        // 校验tag
        $tags = LoggerKeys::$allTags;
        if (!in_array($tag, $tags)) {
            throw new LibraryException("TAG:{$tag} 需要在LoggerKeys中定义！");
        }

        // 获取错误信息
        if (is_subclass_of($msg, 'Exception')) {
            $traceList  = $msg->getTrace();
            $message    = $msg->getMessage();
            $traceInfo  = $traceList[0];
            $loc        = $traceInfo['file'] . ':' . $traceInfo['line'];
        } else {
            $traceList  = debug_backtrace();
            $message    = $msg;
            $traceInfo  = $traceList[1];
            $loc        = $traceInfo['file'] . ':' . $traceInfo['line'];
        }

        $now = time();
        $data = array(
            'create_time'   => $now,  // 日志发生的时间，应该由外部传入，因为可能是异步
            'update_time'   => $now,
            'tag'           => $tag,
            'level'         => $level,
            'client_ip'     => Http::getClientIp(),
            'client_port'   => Http::getClientPort(),
            'server_ip'     => Http::getServerIp(),
            'server_port'   => Http::getServerPort(),
            'url'           => Url::getCurrentUrl(),
            'post'          => json_encode($_POST),
            'loc'           => $loc,
            'message'       => $message,
            'trace'         => json_encode($traceList),
        );

        if (GlobalConfig::$LOGGER_ASYNC) {
            $gearman = GearmanPool::getClient(GearmanConfig::$SERVER_COMMON);
            $gearman->doBackground('logger_async', json_encode($data));
        } else {
            LoggerInterface::save($data);
        }

        GlobalConfig::$LOGGER_ENABLE = $tmpEnable;
    }
}
