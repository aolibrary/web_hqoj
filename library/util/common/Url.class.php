<?php

class Url {

    // 域名类型，长的放前面
    private static $topLevelDomain = array(
        'com.cn',
        'com', 'net', 'cn',
    );

    /**
     * 代码中的重定向都使用302
     *
     * @param   $url    string
     */
    public static function redirect($url) {

        header('HTTP/1.1 302 Moved Temporarily');
        header('Location: ' . $url);
        exit;
    }

    public static function redirect404() {

        header('HTTP/1.1 404 Not Found');
        header('Status: 404 Not Found');
        exit;
    }

    public static function refresh() {

        $url = $_SERVER['REQUEST_URI'];
        header('HTTP/1.1 302 Moved Temporarily');
        header('Location: ' . $url);
        exit;
    }

    /**
     * 创建一个URL
     *
     * @param   $url            string  url
     * @param   $otherParams    array   参数重写
     * @return  string          new url
     */
    public static function make($url = '', $otherParams) {

        $queryStr = self::getQueryString($url, $otherParams);
        $domain   = self::getDomain($url, true, true);
        $path     = self::getPath($url);
        $wenhao   = empty($queryStr) ? '' : '?';
        return $domain . $path . $wenhao . $queryStr;
    }

    /**
     * 获取当前完整url
     *
     * @param   $otherParams    array   支持对参数进行重新赋值
     * @return  string
     */
    public static function getCurrentUrl($otherParams = array()) {

        $uri = Arr::get('REQUEST_URI', $_SERVER, '', true);
        if (empty($uri)) {
            return '';
        }

        $domain = self::getDomain('', true, true);
        if (empty($otherParams)) {
            return $domain . $uri;
        }
        $path     = self::getPath();
        $queryStr = self::getQueryString('', $otherParams);
        $wenhao   = empty($queryStr) ? '' : '?';
        return $domain . $path . $wenhao . $queryStr;
    }

    /**
     * 获取主机名
     * 比如当前url为http://xx.domain.com/list/?page=2&user=1，那么返回http://xx.domain.com
     *
     * @param   $url            string  地址
     * @param   $withScheme     boolean 返回值是否带协议
     * @param   $withPort       boolean 是否带端口号，如果是80端口，就不会返回
     * @return  string
     */
    public static function getDomain($url = '', $withScheme = false, $withPort = false) {

        // 如果给定url
        if (!empty($url)) {
            if (0 !== strpos($url, '//') && 0 !== strpos($url, 'http')) {
                $url = '//' . $url;
            }
            $urlArr = parse_url($url);
            $ret = $urlArr['host'];
            if (empty($ret)) {
                return false;
            }
            if ($withScheme && isset($urlArr['scheme'])) {
                $ret = $urlArr['scheme'] . '://' . $urlArr['host'];
            } else if ($withScheme) {
                $ret = '//' . $urlArr['host'];
            }
            if ($withPort && isset($urlArr['port'])) {
                $ret = $ret . ':' . $urlArr['port'];
            }
            return strtolower($ret);
        }

        return self::getCurrentDomain($withScheme, $withPort);
    }

    private static function getCurrentDomain($withScheme = false, $withPort = false) {

        // HTTP_HOST是带端口的
        if (!array_key_exists('HTTP_HOST', $_SERVER)) {
            return '';
        }
        $hostArr = explode(':', $_SERVER['HTTP_HOST']);
        $domain = $hostArr[0];

        if ($withScheme) {
            if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
                $domain = 'https://' . $domain;
            } else {
                $domain = 'http://' . $domain;
            }
        }
        if ($withPort && $_SERVER['SERVER_PORT'] != 80) {
            $domain = $domain . ':' . $_SERVER['SERVER_PORT'];
        }
        return strtolower($domain);
    }

    /**
     * 比如当前url为http://xx.domain.com/list/?page=2&user=1，那么返回/list/
     *
     * @param   $url    string  如果为空，那么取当前url
     * @return  string
     */
    public static function getPath($url = '') {

        if (!empty($url) && strpos($url, '.') && 0 !== strpos($url, '/') && 0 !== strpos($url, 'http')) {
            $url = '//' . $url;
        }
        $url = empty($url) ? $_SERVER['REQUEST_URI'] : $url;
        $urlArr = parse_url($url);
        $path = array_key_exists('path', $urlArr) ? $urlArr['path'] : '/';
        return $path;
    }

    /**
     * 获取给定url的参数列表
     * 比如给定url为http://xx.domain.com/list/?page=2&user=1，那么返回page=2&user=1
     *
     * @param  $url             string  如果为空，那么取当前url
     * @param  $otherParams     array   支持对参数进行重新赋值
     * @return string
     */
    public static function getQueryString($url = '', $otherParams = array()) {

        $url = empty($url) ? $_SERVER['REQUEST_URI'] : $url;
        $urlArr = parse_url($url);
        $queryString = isset($urlArr['query']) ? $urlArr['query'] : '';
        $params = array();
        parse_str($queryString, $params);
        if (!empty($otherParams) && is_array($otherParams)) {
            foreach ($otherParams as $key => $value) {
                $params[$key] = $value;
            }
        }
        return http_build_query($params);
    }

    /**
     * 获取给定url的参数列表
     *
     * @param   $url    string  需要获取的url，如果url为当前url，那么这个函数相当于$_GET
     * @param   $key    string  需要获取的参数
     * @return  string
     */
    public static function getQueryParam($url, $key) {

        if (empty($url) || empty($key)) {
            return false;
        }
        $urlArr = parse_url($url);
        $queryString = isset($urlArr['query']) ? $urlArr['query'] : '';
        $params = array();
        parse_str($queryString, $params);
        return $params[$key];
    }

    /**
     * 获取域名
     *
     * @param   $url    string  需要获取的url
     * @param   $level  int     0顶级，1当前域名，2二级域名，以此类推
     * @return  string
     */
    public static function getLevelDomain($url = '', $level = 0) {

        $domain = self::getDomain($url);
        if (empty($domain)) {
            return false;
        }
        if (filter_var($url, FILTER_VALIDATE_IP)) {
            return $level > 1 ? false : $domain;
        }
        $suffix = '';
        foreach (self::$topLevelDomain as $value) {
            if ($value == substr($domain, -strlen($value))) {
                $suffix = $value;
                break;
            }
        }
        if (empty($suffix)) {
            $arr = explode('.', $domain);
            $suffix = array_pop($arr);
        }
        $rest = substr($domain, 0, strlen($domain)-strlen($suffix)-1);
        if ($level == 0) {
            return $suffix;
        }
        $restArr = explode('.', $rest);
        if (count($restArr) <= $level) {
            return $rest . '.' . $suffix;
        }
        $ret = $suffix;
        for ($i = 1; $i <= $level; $i++) {
            $ret = $restArr[count($restArr)-$i] . '.' . $ret;
        }
        return $ret;
    }

}