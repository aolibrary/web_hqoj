<?php

class Util {

    /**
     * 判断脚本是否是控制台运行
     *
     * @return  bool
     */
    public static function isConsole() {
        if (ini_get('html_errors')) {
            return false;
        }
        return true;
    }

    public static function isInt($value) {
        return (is_numeric($value) && intval($value) == $value);
    }


}