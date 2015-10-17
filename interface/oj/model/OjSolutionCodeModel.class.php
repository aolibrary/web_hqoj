<?php

class OjSolutionCodeModel extends BaseModel {

    public function __construct($handle = null) {

        $this->masterServer = DbConfig::$SERVER_MASTER;
        $this->slaveServer  = DbConfig::$SERVER_SLAVE;
        $this->dbName       = DbConfig::DB_HQOJ;
        $this->tableName    = 'oj_solution_code';
        $this->fieldTypes   = array(
            'id'          => 'int',  // 
            'create_time' => 'int',  // 创建时间
            'update_time' => 'int',  // 更新时间
            'solution_id' => 'int',  // solution_id
            'source'      => 'text', // 代码
        );
        parent::__construct($handle);
    }

}