<?php

class View {

    public $dir = '';

    public function __construct($dir) {
        if (!is_dir($dir)) {
            throw new FrameworkException("目录{$dir}不存在！");
        }
        $this->dir = $dir;
    }

    public function assign($params) {
        foreach ($params as $key => $value) {
            $this->$key = $value;
        }
    }

    public function fetch($params, $tpl) {

        // 如果是绝对路径
        if (0 === strpos($tpl, '/')) {
            $path = $tpl;
        } else {
            $path = rtrim($this->dir, '/') . '/' . $tpl;
        }
        if (!is_file($path)) {
            throw new FrameworkException("模板文件{$tpl}不存在！");
        }
        $this->assign($params);
        ob_start();
        include $path;
        $ret = ob_get_contents();
        ob_end_clean();
        return $ret;
    }
}
