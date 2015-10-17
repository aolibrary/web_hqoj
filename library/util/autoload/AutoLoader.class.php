<?php

class AutoLoader {

    public static function autoLoad($className) {

        // 解除名空间限制
        $arr = explode('\\', $className);
        $className = end($arr);

        $paths = get_include_path();
        $paths = !empty($paths) ? explode(PATH_SEPARATOR, $paths) : array();
        foreach ($paths as $path) {
            $file = $path . '/' . $className . '.class.php';
            if (is_file($file)) {
                require_once $file;
                return ;
            }
        }
    }

    public static function setAutoDir($path) {

        if (!is_dir($path)) {
            return ;
        }

        // 防止重复
        $paths = get_include_path();
        $paths = !empty($paths) ? explode(PATH_SEPARATOR, $paths) : array();
        if (in_array($path, $paths)) {
            return;
        }

        $path = rtrim($path, '/');
        set_include_path(get_include_path() . PATH_SEPARATOR . $path);
        $handle = opendir($path);
        while (false !== ($file = readdir($handle))) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            $file = $path . '/' . $file;
            if (is_dir($file)) {
                self::setAutoDir($file);
            }
        }
    }
}
