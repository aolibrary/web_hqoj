<?php

return array(
    array(
        'title' => '一级菜单',
        'code'  => '/backend/default/test1',
        'menu'  => array(
            array(
                'title'     => '二级菜单',
                'url'       => '/test/',
            ),
            array(
                'title'     => '测试权限',
                'url'       => '/test/testPremission/',
                'code'      => '/backend/default/test2'
            ),
            array(
                'title'     => '测试隐藏',
                'url'       => '/test/testHidden/',
                'hidden'    => true,    // 可以对这个二级菜单设置权限，但是不会在菜单栏展示；不过会在面包屑中展示
            ),
        ),
    ),
    array(
        'title' => '一级菜单2',
        'menu'  => array(
            array(
                'title'     => '跳转到百度',
                'url'       => '//www.baidu.com/',
            ),
        ),
    ),
);