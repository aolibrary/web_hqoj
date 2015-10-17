<?php

class Debug {

    // 第一次使用p时的毫秒时间戳
    private static $firstTime = 0;
    private static $debugList = array();

    /**
     * 监控信息
     *
     * @param   string  $msg    记录的消息
     */
    public static function p($msg) {

        if (!GlobalConfig::$DEBUG_ENABLE) {
            return;
        }

        if (empty(self::$firstTime)) {
            self::$firstTime = Time::ms();
            $debugInfo = array(
                'msg'   => '初始化',
                'time'  => date('Y-m-d H:i:s', time()),
            );
            self::$debugList[] = $debugInfo;
        }
        $time = sprintf('%.2lf', Time::ms()-self::$firstTime);
        $debugInfo = array(
            'msg'   => $msg,
            'time'  => $time . 'MS',
        );
        self::$debugList[] = $debugInfo;
    }

    public static function show() {

        if (Util::isConsole()) {
            foreach (self::$debugList as $debugInfo) {
                echo "\n";
                echo $debugInfo['msg'] . ' ----- ' . $debugInfo['time'];
            }
        } else {
            foreach (self::$debugList as $debugInfo) {
                echo '<p>';
                echo $debugInfo['msg'] . ' ----- ' . $debugInfo['time'];
                echo '</p>';
            }
        }
    }

}