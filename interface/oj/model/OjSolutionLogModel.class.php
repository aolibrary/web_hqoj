<?php

class OjSolutionLogModel extends BaseModel {

    public function __construct($handle = null) {

        $this->masterServer = DbConfig::$SERVER_MASTER;
        $this->slaveServer  = DbConfig::$SERVER_SLAVE;
        $this->dbName       = DbConfig::DB_HQOJ;
        $this->tableName    = 'oj_solution_log';
        $this->fieldTypes   = array(
            'id'          => 'int',  // 自增id
            'create_time' => 'int',  // 创建时间
            'update_time' => 'int',  // 更新时间
            'solution_id' => 'int',  // 
            'ce'          => 'text', // 编译错误
            're'          => 'text', // 运行错误
            'detail'      => 'text', // 运行详情
        );
        parent::__construct($handle);
    }

}