<?php

class Curl {

    // 当前连接句柄
    private $handle = null;

    // 最近一次exec耗时
    private $lastExecTime = 0;

    // 常用的配置，默认值
    private $defaultOption = array(
        CURLOPT_CONNECTTIMEOUT => 60,    // 连接超时
        CURLOPT_TIMEOUT        => 60,    // 执行超时
        CURLOPT_RETURNTRANSFER => true,  // 以字符串形式返回
        CURLOPT_HEADER         => false, // 默认不输出头文件
        CURLOPT_AUTOREFERER    => true,  // 自动设置header中的Referer:信息
        CURLOPT_FOLLOWLOCATION => true,  // 返回跳转的信息
    );

    public function __construct() {

        $this->handle = curl_init();
        curl_setopt_array($this->handle, $this->defaultOption);
    }

    public function setOption($option) {

        curl_setopt_array($this->handle, $option);
    }

    public function setCookieFile($file) {

        // 创建文件
        if (!is_file($file)) {
            $dir = dirname($file);
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }
            touch($file);
        }
        $option = array(
            CURLOPT_COOKIEFILE => $file,
            CURLOPT_COOKIEJAR  => $file,
        );
        curl_setopt_array($this->handle, $option);
    }

    public function login($url, $data) {

        return $this->post($url, $data);
    }

    public function post($url, $data) {

        $data = http_build_query($data);
        $option = array(
            CURLOPT_URL  => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $data,
        );
        curl_setopt_array($this->handle, $option);

        $begin = Time::ms();
        $content = curl_exec($this->handle);
        $end = Time::ms();
        $this->lastExecTime = $end-$begin;

        if (curl_errno($this->handle)) {
            Logger::error('library', $this->error());
            return false;
        }
        return $content;
    }

    public function get($url) {

        $option = array(
            CURLOPT_URL  => $url,
            CURLOPT_POST => false,
        );
        curl_setopt_array($this->handle, $option);

        $begin = Time::ms();
        $content = curl_exec($this->handle);
        $end = Time::ms();
        $this->lastExecTime = $end-$begin;

        if (curl_errno($this->handle)) {
            Logger::error('library', $this->error());
            return false;
        }
        return $content;
    }

    /**
     * 获取上次curl_exec的耗时，单位毫秒
     *
     * @return  int     毫秒耗时
     */
    public function getLastExecTime() {

        return $this->lastExecTime;
    }

    public function close() {

        curl_close($this->handle);
    }

    public function errno() {

        return curl_errno($this->handle);
    }

    public function error() {

        return curl_error($this->handle);
    }
}
