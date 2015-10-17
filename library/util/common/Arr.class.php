<?php

class Arr {

    /**
     * 从数组中拿到一个值
     *
     * @param   string      $key
     * @param   array       $arr
     * @param   string      $default        默认值
     * @param   bool|false  $emptyReplace   值为空时是否要置换
     * @return  mixed
     */
    public static function get($key, $arr, $default = '', $emptyReplace = false) {
        if (empty($arr) || !is_array($arr) || !array_key_exists($key, $arr) || ($emptyReplace && empty($arr[$key]))) {
            return $default;
        }
        return $arr[$key];
    }

    /**
     * 从数组中获取参数，初始化为新的数组，将这些值初始化为新的参数
     * 如果字段不存在，那么会被过滤
     *
     * @param   array   $arr    源数组
     * @param   array   $keys   array( 键值 => 强制类型 )
     * @return  array   新的数组
     * @throws  LibraryException
     */
    public static function filter($arr, $keys) {
        $ret = array();
        foreach ($keys as $key) {
            if (!array_key_exists($key, $arr)) {
                continue;
            }
            $ret[$key] = $arr[$key];
        }
        return $ret;
    }

    /**
     * 从$list索引数组中，取出每行的一个字段作为键，然后返回关联数组
     * 如果行中不存在这个键，那么会报warning，同时这行会被过滤；如果键重复，会覆盖之前的行
     *
     * @param   int|string  $key        存在$fullList中的键
     * @param   array       $fullList   完整的二维索引数组
     * @return  array       返回一个新的关联数组
     * @throws  LibraryException
     */
    public static function listToHash($key, Array $fullList) {

        // 如果为空，返回
        if (empty($fullList)) {
            return array();
        }

        $retList = array();
        $warn   = false;
        foreach ($fullList as $row) {
            // 键不存在
            if (!is_array($row)) {
                throw new LibraryException('$fullList必须是二维数组！');
            }
            if (!array_key_exists($key, $row)) {
                $warn = true;
                continue;
            }
            $retList[$row[$key]] = $row;
        }

        // 只警告一次
        if ($warn) {
            Logger::warn('library', "数组中不存在键：{$key}");
            trigger_error("数组中不存在键：{$key}", E_USER_WARNING);
        }
        return $retList;
    }

}