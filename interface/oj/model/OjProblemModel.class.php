<?php

class OjProblemModel extends BaseModel {

    public function __construct($handle = null) {

        $this->masterServer = DbConfig::$SERVER_MASTER;
        $this->slaveServer  = DbConfig::$SERVER_SLAVE;
        $this->dbName       = DbConfig::DB_HQOJ;
        $this->tableName    = 'oj_problem';
        $this->fieldTypes   = array(
            'id'            => 'int',     // 自增id
            'create_time'   => 'int',     // 创建时间
            'update_time'   => 'int',     // 更新时间
            'remote'        => 'int',     // 远程OJ标识
            'problem_id'    => 'varchar', // 题目id
            'problem_code'  => 'varchar', // 题目code
            'title'         => 'varchar', // 标题
            'description'   => 'text',    // 描述
            'input'         => 'text',    // 输入描述
            'output'        => 'text',    // 输出描述
            'sample_input'  => 'text',    // 输入例子
            'sample_output' => 'text',    // 输出例子
            'hint'          => 'text',    // 提示
            'source'        => 'varchar', // 来源
            'time_limit'    => 'int',     // 时间限制，单位ms
            'memory_limit'  => 'int',     // 内存限制，单位KB
            'hidden'        => 'int',     // 是否隐藏
            'solved'        => 'int',     // 总AC人数
            'submit'        => 'int',     // 总提交次数
            'user_id'       => 'varchar', // 题目负责人
            'audit'         => 'int',     // 题目公开审核状态
            'audit_history' => 'text',    // 审核历史
        );
        parent::__construct($handle);
    }

}