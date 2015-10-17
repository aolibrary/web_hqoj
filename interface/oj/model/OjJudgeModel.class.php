<?php

class OjJudgeModel extends BaseModel {

    public function __construct($handle = null) {

        $this->masterServer = DbConfig::$SERVER_MASTER;
        $this->slaveServer  = DbConfig::$SERVER_SLAVE;
        $this->dbName       = DbConfig::DB_HQOJ;
        $this->tableName    = 'oj_judge';
        $this->fieldTypes   = array(
            'id'           => 'int',     // 自增ID
            'create_time'  => 'int',     // 创建时间
            'update_time'  => 'int',     // 更新时间
            'problem_id'   => 'varchar', // 题目ID
            'language'     => 'int',     // 编译器
            'time_limit'   => 'int',     // 时间限制
            'memory_limit' => 'int',     // 内存限制
            'source'       => 'text',    // 代码
            'code_length'  => 'int',     // 代码长度
            'result'       => 'int',     // 结果
            'time_cost'    => 'int',     // 时间消耗
            'memory_cost'  => 'int',     // 内存消耗
            'judge_time'   => 'int',     // 评判时间
            'ce'           => 'text',    // 编译错误
            're'           => 'text',    // 运行错误
            'detail'       => 'text',    // 运行详情
            'solution_id'  => 'int',     // HQOJ上对应的solutionId
            'user_id'      => 'int',     // 提交的用户ID
        );
        parent::__construct($handle);
    }

}