<?php

class Time {

    /**
     * 返回当前毫秒级时间戳
     *
     * @return  int     毫秒时间戳
     */
    public static function ms() {

        list($usec, $sec) = explode(' ', microtime());
        return intval(1000 * ($sec + $usec));
    }

    /**
     * 返回中文星期
     *
     * @param   int     $week   时间戳
     * @return  string  中文的星期几
     */
    public static function getWeekCn($week) {

        $arr = array('一', '二', '三', '四', '五', '六', '日');
        $key = date('N', $week);
        return '星期' . $arr[$key];
    }
}