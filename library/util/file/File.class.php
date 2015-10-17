<?php

class File {

    /**
     * 将内容写入文件
     *
     * @param   string  $path       绝对路径，保存的文件名，如果路径不存在，那么会被新建
     * @param   string  $content    写入文件的内容
     * @return  boolean
     */
    public static function write($path, $content) {

        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $ret = file_put_contents($path, $content);
        return $ret;
    }

    /**
     * 读取文件内容，如果读取失败或者文件不存在返回false
     *
     * @param   string  $path   文件路径
     * @return  string
     */
    public static function read($path) {

        return file_get_contents($path);
    }

    /**
     * 将一个变量序列化后存入文件，并且可以设置过期时间
     *
     * @param   string  $path       保存的文件名
     * @param   mixed   $data       保存的数据
     * @param   int     $expireAt   过期时间，时间戳，如果是0的话，那么保存到2099年
     * @return  boolean
     */
    public static function set($path, $data, $expireAt = 0) {

        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // 序列化
        $data = gzcompress(serialize($data), 3);

        // 过期时间
        $timeStr = (0 == $expireAt) ? '2099-01-01 08:00:00' : date('Y-m-d H:i:s', $expireAt);

        $data = $timeStr . "\n" . $data;
        $ret  = file_put_contents($path, $data);
        return $ret;
    }

    public static function get($path) {

        if (!is_file($path)) {
            Logger::warn('library', "Data缓存文件{$path}不存在！");
            return false;
        }

        $content = file_get_contents($path);
        if (false === $content) {
            return false;
        }

        // 获取时间
        $expireAt = substr($content, 0, 19);
        $time     = strtotime($expireAt);
        if ($time < time()) {
            self::delete($path);
            return false;
        }

        // 保存的内容
        $data = substr($content, 20);
        $data = unserialize(gzuncompress($data));
        return $data;
    }

    public static function delete($path) {

        return unlink($path);
    }
}
