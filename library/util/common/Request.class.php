<?php

class Request {

    private static $scriptTags = array('\?php', 'script');

    public static function isAjax() {

        return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'));
    }

    public static function isPost() {

        return $_SERVER['REQUEST_METHOD'] == 'POST';
    }

    public static function isGet() {

        return $_SERVER['REQUEST_METHOD'] == 'GET';
    }

    public static function getGET($key, $default = '', $enableHtml = false, $enableScript = false) {

        if (array_key_exists($key, $_GET)) {
            if (!$enableHtml) {
                return strip_tags($_GET[$key]);
            } else {
                return !$enableScript ? Html::stripTags($_GET[$key], self::$scriptTags) : $_GET[$key];
            }
        }
        return $default;
    }

    public static function getPOST($key, $default = '', $enableHtml = false, $enableScript = false) {

        if (array_key_exists($key, $_POST)) {
            if (!$enableHtml) {
                return strip_tags($_POST[$key]);
            } else {
                return !$enableScript ? Html::stripTags($_POST[$key], self::$scriptTags) : $_POST[$key];
            }
        }
        return $default;
    }

    public static function getREQUEST($key, $default = '', $enableHtml = false, $enableScript = false) {

        if (array_key_exists($key, $_REQUEST)) {
            if (!$enableHtml) {
                return strip_tags($_REQUEST[$key]);
            } else {
                return !$enableScript ? Html::stripTags($_REQUEST[$key], self::$scriptTags) : $_REQUEST[$key];
            }
        }
        return $default;
    }

}