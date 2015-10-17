<?php

class OjSolutionModel extends BaseModel {

    public function __construct($handle = null) {

        $this->masterServer = DbConfig::$SERVER_MASTER;
        $this->slaveServer  = DbConfig::$SERVER_SLAVE;
        $this->dbName       = DbConfig::DB_HQOJ;
        $this->tableName    = 'oj_solution';
        $this->fieldTypes   = array(
            'id'                    => 'int',     // 全局唯一id
            'create_time'           => 'int',     // 创建时间
            'update_time'           => 'int',     // 更新时间
            'problem_global_id'     => 'int',     // oj_problem表中的id
            'remote'                => 'int',     // OJ标识
            'problem_id'            => 'varchar', // 题目id
            'problem_code'          => 'varchar', // 题目code
            'user_id'               => 'int',     // 用户id
            'time_cost'             => 'int',     // 运行时间
            'memory_cost'           => 'int',     // 运行内存
            'submit_time'           => 'int',     // 提交时间
            'submit_ip'             => 'bigint',  // 提交IP
            'language'              => 'int',     // 语言
            'result'                => 'int',     // 结果
            'code_length'           => 'int',     // 代码长度
            'judge_time'            => 'int',     // 评判时间
            'run_id'                => 'int',     // 对应OJ的runid
            'hidden'                => 'int',     // 状态隐藏
            'remote_uid'            => 'int',     // 远程OJ帐号标识
            'share'                 => 'int',     // 分享选项
            'contest_id'            => 'int',     // 所在竞赛id
            'contest_submit_order'  => 'int',     // 比赛中第几个提交
            'contest_submit_second' => 'int',     // 比赛中第几秒提交的
            'contest_end_time'      => 'int',     // 比赛结束时间
        );
        parent::__construct($handle);
    }

}