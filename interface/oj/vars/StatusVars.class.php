<?php

class StatusVars {

    // 枚举OJ
    const REMOTE_HQU = 0;
    const REMOTE_HDU = 1;
    const REMOTE_POJ = 2;
    const REMOTE_ZOJ = 3;

    // 远程的OJ
    public static $REMOTE_SCHOOL = array(
        self::REMOTE_HQU => 'hqu',
        self::REMOTE_HDU => 'hdu',
        self::REMOTE_POJ => 'poj',
        self::REMOTE_ZOJ => 'zoj',
    );

    // 远程的OJ学校名称
    public static $REMOTE_SCHOOL_NAME = array(
        self::REMOTE_HQU => '华侨大学',
        self::REMOTE_HDU => '杭州电子科技大学',
        self::REMOTE_POJ => '北京大学',
        self::REMOTE_ZOJ => '浙江大学',
    );

    // 时间限制
    public static $TIME_LIMIT = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10);
    // 内存限制
    public static $MEMORY_LIMIT = array(32, 64, 128, 256, 512);

    // 枚举编译器
    const GCC       = 1;
    const GPLUS     = 2;
    const CC        = 3;
    const CXX       = 4;
    const JAVA      = 5;

    public static $LANGUAGE_FORMAT = array(
        self::GCC       => 'Gcc',
        self::GPLUS     => 'G++',
        self::CC        => 'C',
        self::CXX       => 'C++',
        self::JAVA      => 'Java',
    );

    // 各大OJ支持的语言
    public static $LANGUAGE_SUPPORT = array(

        self::REMOTE_HQU    => array(
            self::CC        => 'C',
            self::CXX       => 'C++',
        ),

        self::REMOTE_HDU    => array(
            self::GCC       => 'Gcc',
            self::GPLUS     => 'G++',
            self::CC        => 'C',
            self::CXX       => 'C++',
            self::JAVA      => 'Java',
        ),

        self::REMOTE_POJ    => array(
            self::GCC       => 'Gcc',
            self::GPLUS     => 'G++',
            self::CC        => 'C',
            self::CXX       => 'C++',
            self::JAVA      => 'Java',
        ),

        self::REMOTE_ZOJ    => array(
            self::CC        => 'C',
            self::CXX       => 'C++',
            self::JAVA      => 'Java',
        ),

    );

    // 枚举状态
    const QUEUE              = 0;
    const REJUDGE            = 1;
    const COMPILING          = 2;
    const RUNNING            = 3;
    const ACCEPTED           = 4;
    const PRESENTATION_ERROR = 5;
    const WRONG_ANSWER       = 6;
    const TIME_EXCEEDED      = 7;
    const MEMORY_EXCEEDED    = 8;
    const OUTPUT_EXCEEDED    = 9;
    const RUNTIME_ERROR      = 10;
    const COMPILATION_ERROR  = 11;
    const TIME_OUT           = 12;
    const INVALID            = 13;

    // 状态文案
    public static $RESULT_FORMAT = array(
        self::QUEUE              => 'Queue',
        self::REJUDGE            => 'Queue（Rejudge）',
        self::COMPILING          => 'Compiling',
        self::RUNNING            => 'Running',
        self::ACCEPTED           => 'Accepted',
        self::PRESENTATION_ERROR => 'Presentation Error',
        self::WRONG_ANSWER       => 'Wrong Answer',
        self::TIME_EXCEEDED      => 'Time Limit Exceeded',
        self::MEMORY_EXCEEDED    => 'Memory Limit Exceeded',
        self::OUTPUT_EXCEEDED    => 'Output Limit Exceeded',
        self::RUNTIME_ERROR      => 'Runtime Error',
        self::COMPILATION_ERROR  => 'Compilation Error',
        self::TIME_OUT           => 'Time Out',
        self::INVALID            => 'Invalid',
    );

    // 状态文案颜色
    public static $RESULT_CLASS = array(
        self::QUEUE              => 'green',
        self::REJUDGE            => 'green',
        self::COMPILING          => 'green',
        self::RUNNING            => 'green',
        self::ACCEPTED           => 'red',
        self::PRESENTATION_ERROR => 'orange',
        self::WRONG_ANSWER       => 'green',
        self::TIME_EXCEEDED      => 'green',
        self::MEMORY_EXCEEDED    => 'green',
        self::OUTPUT_EXCEEDED    => 'green',
        self::RUNTIME_ERROR      => 'blue',
        self::COMPILATION_ERROR  => 'blue',
        self::TIME_OUT           => 'gray',
        self::INVALID            => 'gray',
    );

    // solution level
    const SOLUTION_PUBLIC       = 0;
    const SOLUTION_PROTECTED    = 1;
    const SOLUTION_PRIVATE      = 2;
    const SOLUTION_SHARE        = 3;

    // level format
    public static $LEVEL_FORMAT = array(
        self::SOLUTION_PUBLIC       => '公开',
        self::SOLUTION_PROTECTED    => '保护',
        self::SOLUTION_PRIVATE      => '保密',
        self::SOLUTION_SHARE        => '分享',
    );

    // level color
    public static $LEVEL_COLOR  = array(
        self::SOLUTION_PUBLIC       => 'red',
        self::SOLUTION_PROTECTED    => 'green',
        self::SOLUTION_PRIVATE      => 'green',
        self::SOLUTION_SHARE        => 'orange',
    );

    // hdu url
    const HDU_LOGIN_URL         = 'http://acm.hdu.edu.cn/userloginex.php?action=login';
    const HDU_SUBMIT_URL        = 'http://acm.hdu.edu.cn/submit.php?action=submit';
    const HDU_STATUS_URL        = 'http://acm.hdu.edu.cn/status.php?user=';
    const HDU_COMPILE_INFO_URL  = 'http://acm.hdu.edu.cn/viewerror.php?rid=';

    // hdu result map
    public static $hduResultMap = array(
        'Accepted'              => StatusVars::ACCEPTED,
        'Wrong Answer'          => StatusVars::WRONG_ANSWER,
        'Presentation Error'    => StatusVars::PRESENTATION_ERROR,
        'Compilation Error'     => StatusVars::COMPILATION_ERROR,
        'Runtime Error'         => StatusVars::RUNTIME_ERROR,
        'Time Limit Exceeded'   => StatusVars::TIME_EXCEEDED,
        'Memory Limit Exceeded' => StatusVars::MEMORY_EXCEEDED,
        'Output Limit Exceeded' => StatusVars::OUTPUT_EXCEEDED,
    );

    // hdu language map
    public static $hduLangMap = array(
        StatusVars::GCC     => 1,
        StatusVars::GPLUS   => 0,
        StatusVars::CC      => 3,
        StatusVars::CXX     => 2,
        StatusVars::JAVA    => 5,
    );

    // poj url
    const POJ_LOGIN_URL         = 'http://poj.org/login';
    const POJ_SUBMIT_URL        = 'http://poj.org/submit';
    const POJ_STATUS_URL        = 'http://poj.org/status?user_id=';
    const POJ_COMPILE_INFO_URL  = 'http://poj.org/showcompileinfo?solution_id=';

    // poj result map
    public static $pojResultMap = array(
        'Accepted'              => StatusVars::ACCEPTED,
        'Wrong Answer'          => StatusVars::WRONG_ANSWER,
        'Presentation Error'    => StatusVars::PRESENTATION_ERROR,
        'Compile Error'         => StatusVars::COMPILATION_ERROR,
        'Runtime Error'         => StatusVars::RUNTIME_ERROR,
        'Time Limit Exceeded'   => StatusVars::TIME_EXCEEDED,
        'Memory Limit Exceeded' => StatusVars::MEMORY_EXCEEDED,
        'Output Limit Exceeded' => StatusVars::OUTPUT_EXCEEDED,
    );

    // poj language map
    public static $pojLangMap = array(
        StatusVars::GCC     => 1,
        StatusVars::GPLUS   => 0,
        StatusVars::CC      => 5,
        StatusVars::CXX     => 4,
        StatusVars::JAVA    => 2,
    );

    // zoj url
    const ZOJ_LOGIN_URL         = 'http://acm.zju.edu.cn/onlinejudge/login.do';
    const ZOJ_SUBMIT_URL        = 'http://acm.zju.edu.cn/onlinejudge/submit.do';
    const ZOJ_STATUS_URL        = 'http://acm.zju.edu.cn/onlinejudge/showRuns.do?contestId=1&handle=';
    const ZOJ_COMPILE_INFO_URL  = 'http://acm.zju.edu.cn/onlinejudge/showJudgeComment.do?submissionId=';

    // zoj result map
    public static $zojResultMap = array(
        'Accepted'              => StatusVars::ACCEPTED,
        'Wrong Answer'          => StatusVars::WRONG_ANSWER,
        'Presentation Error'    => StatusVars::PRESENTATION_ERROR,
        'Compilation Error'     => StatusVars::COMPILATION_ERROR,
        'Segmentation Fault'    => StatusVars::RUNTIME_ERROR,
        'Non-zero Exit Code'    => StatusVars::RUNTIME_ERROR,
        'Floating Point Error'  => StatusVars::RUNTIME_ERROR,
        'Runtime Error'         => StatusVars::RUNTIME_ERROR,
        'Time Limit Exceeded'   => StatusVars::TIME_EXCEEDED,
        'Memory Limit Exceeded' => StatusVars::MEMORY_EXCEEDED,
        'Output Limit Exceeded' => StatusVars::OUTPUT_EXCEEDED,
    );

    // zoj language map
    public static $zojLangMap = array(
        StatusVars::CC      => 1,
        StatusVars::CXX     => 2,
        StatusVars::JAVA    => 4,
    );

}
