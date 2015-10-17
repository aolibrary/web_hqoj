<?php

class Http {

    public static function ip2long($ip) {
        return sprintf('%u', ip2long($ip));
    }

    public static function long2ip($long) {
        return long2ip($long);
    }

    public static function getClientIp() {

        // 如果不是http请求
        if (!ini_get('html_errors')) {
            return 0;
        }

        $ip = getenv('HTTP_CLIENT_IP');
        if($ip && strcasecmp($ip, 'unknown') && !preg_match('/192\.168\.\d+\.\d+/', $ip)) {
            return self::ip2long($ip);
        }
        $ip = getenv('HTTP_X_FORWARDED_FOR');
        if($ip && strcasecmp($ip, 'unknown')) {
            return self::ip2long($ip);
        }
        $ip = getenv('REMOTE_ADDR');
        if($ip && strcasecmp($ip, 'unknown')) {
            return self::ip2long($ip);
        }
        if (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
            if($ip && strcasecmp($ip, 'unknown')) {
                return self::ip2long($ip);
            }
        }
        return 0;
    }

    public static function getClientPort() {
        return (int) Arr::get('REMOTE_PORT', $_SERVER, 0);
    }

    public static function getServerIp() {
        $ip = Arr::get('SERVER_ADDR', $_SERVER, '');
        return empty($ip) ? 0 : self::ip2long($ip);
    }

    public static function getServerPort() {
        return (int) Arr::get('SERVER_PORT', $_SERVER, 0);
    }

}