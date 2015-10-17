<?php

class BackendConfig {

    // 如果要将网站添加到后台，可以在此注册
    public static $PROJECT_LIST = array(

        // 默认后台首页
        array(
            'title' => '默认后台',
            'url'   => '//bc.hqoj.net',
            'code'  => '/backend/default/default',
            'menu'  => 'default.menu.inc.php',
        ),

        // 权限管理
        array(
            'title' => '权限系统',
            'url'   => '//root.hqoj.net',
            'menu'  => 'root.menu.inc.php',
        ),

        // 日志管理后台
        array(
            'title' => '日志监控',
            'url'   => '//log.bc.hqoj.net',
            'code'  => '/backend/log/default',
            'menu'  => 'log.menu.inc.php',
        ),

        // OJ管理员后台
        array(
            'title' => 'OJ管理员后台',
            'url'   => '//admin.hqoj.net',
            'code'  => '/hqoj/admin',
            'menu'  => 'oj.menu.inc.php',
        ),

        // 文章发布系统
        array(
            'title' => '文章发布后台',
            'url'   => '//doc.bc.hqoj.net',
            'code'  => '/backend/doc/default',
            'menu'  => 'doc.menu.inc.php',
        )
    );
}
