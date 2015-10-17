<?php

class Cookie {

    private static function checkKey($key) {

        if (!in_array($key, CookieKeys::$forceKeys)) {
            throw new LibraryException("KEY:{$key} 需要在CookieKeys中定义！");
        }
    }

    public static function set($key, $value, $expireAt = 0, $path = '/', $domain = '') {

        if (empty($domain)) {
            $domain = Url::getLevelDomain('', 1);
            if (!filter_var($domain, FILTER_VALIDATE_IP)) {
                $domain = '.' . $domain;
            }
        }

        self::checkKey($key);
        $_COOKIE[$key] = $value;
        return setcookie($key, $value, $expireAt, $path, $domain);
    }

    public static function get($key, $default = null, $enableHtml = false) {

        self::checkKey($key);
        if (isset($_COOKIE[$key])) {
            return !$enableHtml ? strip_tags($_COOKIE[$key]) : $_COOKIE[$key];
        }
        return $default;
    }

    public static function delete($key, $path = '/', $domain = '') {

        if (empty($domain)) {
            $domain = Url::getLevelDomain('', 1);
            if (!filter_var($domain, FILTER_VALIDATE_IP)) {
                $domain = '.' . $domain;
            }
        }

        self::checkKey($key);
        unset($_COOKIE[$key]);
        return setcookie($key, '', time()-1, $path, $domain);
    }
}