<?php

return array(
    array(
        'title' => '管理员',
        'menu'  => array(
            array(
                'title'     => '管理员',
                'url'       => '/manager_list/',
            ),
            array(
                'title'     => '无效路径',
                'url'       => '/manager_invalid/',
            ),
        ),
    ),
    array(
        'title' => '权限',
        'menu'  => array(
            array(
                'title'     => '权限树',
                'url'       => '/permission_tree/',
            ),
            array(
                'title'     => '权限列表',
                'url'       => '/permission_list/',
            ),
        )
    ),
);