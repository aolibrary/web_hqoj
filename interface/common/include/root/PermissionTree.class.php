<?php

class PermissionTree {

    private $_tree = array();

    public function __construct($state = array()) {

        $this->_tree = array(
            'id'    => '-1',
            'data'  => 'root',
            'text'  => '权限系统',
            'state' => $state,
            'icon'  => '//sta.hqoj.net/js/util/jquery/plugin/jstree/icon/tree_icon.png',
            'children'  => array(),
        );
    }

    public function insert($id, $code, $description, $state = array()) {

        // 校验code
        if (!self::isValidCode($code)) {
            throw new InterfaceException('权限码不合法！');
        }

        // 拆分code
        $arr = explode('/', trim($code, '/'));

        // pop出叶子
        $leaf = array_pop($arr);

        // 定义指针
        $p = & $this->_tree;

        // 生成路径
        $prefix = '';
        foreach ($arr as $next) {

            $prefix = $prefix . '/' . $next;

            // 如果next已经是叶子节点，无法生成路径
            if ($p['data'] == 'code') {
                throw new InterfaceException('无法在叶子节点上生成路径！');
            }

            // 如果next节点不存在，那么生成一个
            if (!array_key_exists($next, $p['children'])) {

                $p['children'][$next] = array(
                    'id'    => $prefix,         // id必须是唯一的
                    'data'  => 'folder',
                    'text'  => $next,
                    'state' => $state,
                    'icon'  => 'jstree-folder',
                    'children'  => array(),
                );;
            }
            $p = & $p['children'][$next];
        }

        // 如果叶子已经存在
        if (array_key_exists($leaf, $p['children'])) {
            throw new InterfaceException('节点已经存在！');
        }

        // 生成叶子节点
        $p['children'][$leaf] = array(
            'id'    => $id,
            'data'  => 'code',
            'text'  => $code . '（' . $description . '）',
            'state' => $state,
            'icon'  => 'jstree-file',
            'children'  => null,
        );
    }

    // 查找path，这个path可能是文件夹，或者权限
    public function find($path) {

        if (!self::isValidPath($path)) {
            throw new InterfaceException('路径不合法！');
        }

        // 查找
        $p = $this->_tree;
        if ('/' == $path) {
            return $p;
        }

        // 拆分path
        $arr = explode('/', trim($path, '/'));
        foreach ($arr as $next) {
            if (empty($p['children']) || !array_key_exists($next, $p['children'])) {
                return null;
            }
            $p = $p['children'][$next];
        }

        // 如果$path以/结尾，那么为文件夹
        if (rtrim($path, '/') == $path && $p['data'] == 'folder'
        || rtrim($path, '/') != $path && $p['data'] == 'code') {
            return false;
        }

        return $p;
    }

    // 将tree转换为符合条件的jstree json格式，顺便排序
    public function getJsTreeJson() {

        $ret = $this->removeKey($this->_tree);
        return json_encode( $ret );
    }

    // 递归去除children的键
    private function removeKey($node) {

        if (null === $node['children']) {
            return $node;
        }
        usort($node['children'], array('PermissionTree', '_cmp'));
        $ret = array();
        foreach ($node['children'] as $children) {
            $ret[] = $this->removeKey($children);
        }

        $node['children'] = $ret;
        return $node;
    }

    // 去除key时排序
    private static function _cmp($a, $b) {

        if (is_array($a['data']) && is_string($b['data'])) {
            return 1;
        }
        if (is_string($a['data']) && is_array($b['data'])) {
            return -1;
        }
        return strcmp($a['text'], $b['text']);
    }

    // code的格式合法性检查
    public static function isValidCode($code) {

        // 必须是/开头，并且不能是根目录
        if (0 !== strpos($code, '/') || strlen($code) < 2 || strlen($code) > 100) {
            return false;
        }

        // 校验每段路径，必须是字母数组，字母开头
        $code = substr($code, 1);
        $arr = explode('/', $code);
        foreach ($arr as $i => $val) {
            if (empty($val) || !preg_match('/^[a-zA-z][a-zA-Z0-9]*$/', $val)) {
                return false;
            }
        }

        return true;
    }

    // folder的格式合法性检查
    public static function isValidFolder($folder) {

        // 必须是/开头
        if (0 !== strpos($folder, '/') || strlen($folder) < 1 || strlen($folder) > 100) {
            return false;
        }

        // 必须标准，必须以'/'结尾，比如：/bc/，'/'
        if (rtrim($folder, '/') == $folder || strlen(rtrim($folder, '/')) != strlen($folder)-1) {
            return false;
        }

        if ('/' == $folder) {
            return true;
        }

        // 校验每段路径，必须是字母数组，字母开头
        $arr = explode('/', trim($folder, '/'));
        foreach ($arr as $i => $val) {
            if (empty($val) || !preg_match('/^[a-zA-z][a-zA-Z0-9]*$/', $val)) {
                return false;
            }
        }

        return true;
    }

    // path的格式合法性检查
    public static function isValidPath($path) {

        // 必须是/开头
        if (0 !== strpos($path, '/') || strlen($path) < 1 || strlen($path) > 100) {
            return false;
        }

        if ('/' == $path) {
            return true;
        }

        // 校验每段路径，必须是字母数组，字母开头
        $arr = explode('/', trim($path, '/'));
        foreach ($arr as $i => $val) {
            if (empty($val) || !preg_match('/^[a-zA-z][a-zA-Z0-9]*$/', $val)) {
                return false;
            }
        }

        return true;
    }

}
