<?php

class OjProblemSetModel extends BaseModel {

    public function __construct($handle = null) {

        $this->masterServer = DbConfig::$SERVER_MASTER;
        $this->slaveServer  = DbConfig::$SERVER_SLAVE;
        $this->dbName       = DbConfig::DB_HQOJ;
        $this->tableName    = 'oj_problem_set';
        $this->fieldTypes   = array(
            'id'             => 'int',     // 专题ID
            'create_time'    => 'int',     // 创建时间
            'update_time'    => 'int',     // 更新时间
            'title'          => 'varchar', // 专题名称
            'user_id'        => 'int',     // 创建人
            'problem_set'    => 'varchar', // 题目global_id的json数据
            'hidden'         => 'int',     // 是否隐藏
            'refresh_at'     => 'int',     // 刷新时间
            'listing_status' => 'int',     // 置顶状态
        );
        parent::__construct($handle);
    }

}