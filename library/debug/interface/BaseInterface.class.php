<?php

// 字段类型，不要超过32767
const TYPE_INT     = 1;    // 整数，包括字符串整数
const TYPE_INT_GT0 = 2;    // 正整数
const TYPE_STR     = 4;    // 字符串
const TYPE_STR_Y   = 8;    // 非空字符串
const TYPE_ARR     = 16;   // 数组
const TYPE_ARR_Y   = 32;   // 非空数组
const TYPE_NUM     = 64;   // 数值，包括字符串数值
const TYPE_BOOL    = 128;  // 布尔值
const TYPE_NULL    = 256;  // 空
const TYPE_OBJ     = 512;  // 对象

abstract class BaseInterface {

    private static function getType($n) {
        $ret = 0;
        if (is_numeric($n) && intval($n) == $n) {
            $ret |= TYPE_INT;
            if ($n > 0) $ret |= TYPE_INT_GT0;
        }
        if (is_string($n)) {
            $ret |= TYPE_STR;
            if (!empty($n)) $ret |= TYPE_STR_Y;
        }
        if (is_array($n)) {
            $ret |= TYPE_ARR;
            if (!empty($n)) $ret |= TYPE_ARR_Y;
        }
        if (is_numeric($n)) {
            $ret |= TYPE_NUM;
        }
        if (is_bool($n)) $ret |= TYPE_BOOL;
        if (is_null($n)) $ret |= TYPE_NULL;
        if (is_object($n)) $ret |= TYPE_OBJ;
        return $ret;
    }

    /**
     * 校验一个参数的类型
     *
     * @param   mixed       $value      需要校验的参数
     * @param   int         $forceType  强制类型，可以用|来实现兼容多个类型
     * @throws  InterfaceException
     */
    protected static function check($value, $forceType) {
        $type = self::getType($value);
        if (0 == ($forceType & $type)) {
            throw new InterfaceException('参数类型错误！');
        }
    }

    /**
     * 校验必传参数
     *
     * @param   array   $params 需要校验的参数
     * @param   array   $keys   键值列表
     * @throws  InterfaceException
     */
    protected static function judge(Array $params, Array $keys) {

        if (!empty($keys) && empty($keys[0])) {
            throw new InterfaceException('judge, 函数参数错误！');
        }
        foreach ($keys as $key) {
            if (!array_key_exists($key, $params)) {
                throw new InterfaceException("judge, 缺少参数：{$key}");
            }
        }
    }

    /**
     * 从数组中获取参数，初始化为新的数组，将这些值初始化为新的参数
     * 如果字段不存在，那么会被过滤，所以使用init之前，先使用judge判断必须字段
     *
     * @param   array   $params     源数组
     * @param   array   $fieldTypes
     * @return  array   新的数组
     * @throws  InterfaceException
     */
    protected static function getAll(Array $params, Array $fieldTypes) {

        if (empty($fieldTypes)) {
            return array();
        }

        $retArr = array();
        foreach ($fieldTypes as $field => $forceType) {

            if (!array_key_exists($field, $params)) {
                continue;
            }

            $retArr[$field] = self::get($field, $params, 0, $forceType);
        }
        return $retArr;
    }

    /**
     * 从数组中获取某个值
     *
     * @param   string|int  $key
     * @param   array       $arr        数组
     * @param   mixed       $default    默认值，只有$key不存在数组中，才会使用默认值。
     * @param   int         $forceType  强制类型，可以用|来实现兼容多个类型
     * @param   bool        $need       参数$key是否必传
     * @return  mixed
     * @throws  InterfaceException
     */
    protected static function get($key, Array $arr, $default, $forceType = 65535, $need = false) {

        // 必传校验
        if ($need && !array_key_exists($key, $arr)) {
            throw new InterfaceException("get, 缺少参数：{$key}");
        }

        // 如果key存在，那么类型校验
        if (array_key_exists($key, $arr)) {
            $type = self::getType($arr[$key]);
            if (0 == ($forceType & $type)) {
                throw new InterfaceException("get, 参数类型错误：{$key}");
            }
        }

        // 如果key不存在，那么使用默认值
        $ret  = !array_key_exists($key, $arr) ? $default : $arr[$key];
        return $ret;
    }

}
