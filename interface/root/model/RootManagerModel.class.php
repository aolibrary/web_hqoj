<?php

class RootManagerModel extends BaseModel {

    public function __construct($handle = null) {

        $this->masterServer = DbConfig::$SERVER_MASTER;
        $this->slaveServer  = DbConfig::$SERVER_SLAVE;
        $this->dbName       = DbConfig::DB_HQOJ;
        $this->tableName    = 'root_manager';
        $this->fieldTypes   = array(
            'id'          => 'int', // 自增ID
            'create_time' => 'int', // 创建时间
            'update_time' => 'int', // 更新时间
            'forbidden'   => 'int', // 是否禁用
            'user_id'     => 'int', // 用户ID
        );
        parent::__construct($handle);
    }

}