<?php

/**
 * 添加规则
 * 1. 单键命名规则：const TEST = 'test'
 * 2. 前缀命名规则：const TEST_PREFIX = 'test_'
 * 3. 小写+下划线组合：const TEST_LONG_PREFIX = 'test_long_'
 */

class RedisKeys {

    // 测试
    const TEST  = 'test';

    // root
    const ROOT_PATH_SET_            = 'root_path_set_';         // 权限系统，每个管理员的权限
    const ROOT_ENABLED_HASH         = 'root_enabled_hash';      // 权限系统，user_id => manager_id （非禁用）映射

}