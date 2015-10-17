<?php

class Session {

    private static $initFinish  = false;

    private static function init() {

        if (false === self::$initFinish) {
            self::$initFinish = true;
            session_start();
        }
    }

    private static function checkKey($key) {

        if (!in_array($key, SessionKeys::$forceKeys)) {
            throw new LibraryException("KEY:{$key} 需要在SessionKeys中定义！");
        }
    }

    public static function set($key, $value) {

        self::init();
        self::checkKey($key);
        $_SESSION[$key] = $value;
        return true;
    }

    public static function get($key, $default = null) {

        self::init();
        self::checkKey($key);
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }
        return $default;
    }

    public static function getSessionId() {

        return session_id();
    }

    public static function delete($key) {

        self::init();
        self::checkKey($key);
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    public static function clean() {

        self::init();
        session_unset();
        session_destroy();
    }
}