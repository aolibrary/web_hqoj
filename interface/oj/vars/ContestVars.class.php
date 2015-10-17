<?php

class ContestVars {

    const TYPE_NONE     = 0; // 未定义
    const TYPE_PUBLIC   = 1; // 公开
    const TYPE_APPLY    = 2; // 报名
    const TYPE_PASSWORD = 3; // 密码

    public static $TYPE_FORMAT = array(
        self::TYPE_NONE     => '未定义',
        self::TYPE_PUBLIC   => '公开',
        self::TYPE_APPLY    => '报名',
        self::TYPE_PASSWORD => '密码',
    );

    public static $TYPE_COLOR = array(
        self::TYPE_NONE     => 'gray',
        self::TYPE_PUBLIC   => 'red',
        self::TYPE_APPLY    => 'orange',
        self::TYPE_PASSWORD => 'green',
    );

    // 竞赛的题目数量限制
    const CONTEST_PROBLEM_LIMIT = 26;

    // 专题中题目数量上限
    const SET_PROBLEM_LIMIT     = 50;

    // 报名状态
    const APPLY_QUEUE    = 0;
    const APPLY_ACCEPTED = 1;
    const APPLY_REJECTED = 2;

    public static $APPLY_FORMAT = array(
        self::APPLY_QUEUE    => '待审核',
        self::APPLY_ACCEPTED => '通过',
        self::APPLY_REJECTED => '拒绝',
    );

    public static $APPLY_COLOR = array(
        self::APPLY_QUEUE    => 'green',
        self::APPLY_ACCEPTED => 'red',
        self::APPLY_REJECTED => 'orange',
    );

    public static $XUEYUAN = array(
        1 => '计算机学院',
        2 => '信息学院',
        3 => '机电学院',
        4 => '土木工程学院',
        5 => '化工学院',
        6 => '建筑学院',
        7 => '材料学院',
        8 => '生物医学学院',
    );

    // 比赛进行状态
    const STATUS_PENDING    = 0;
    const STATUS_RUNNING    = 1;
    const STATUS_PASSED     = 2;
}
