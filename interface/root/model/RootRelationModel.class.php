<?php

class RootRelationModel extends BaseModel {

    public function __construct($handle = null) {

        $this->masterServer = DbConfig::$SERVER_MASTER;
        $this->slaveServer  = DbConfig::$SERVER_SLAVE;
        $this->dbName       = DbConfig::DB_HQOJ;
        $this->tableName    = 'root_relation';
        $this->fieldTypes   = array(
            'id'          => 'int',     // 自增id
            'create_time' => 'int',     // 创建时间
            'update_time' => 'int',     // 更新时间
            'manager_id'  => 'int',     // 管理员ID
            'path'        => 'varchar', // 路径
        );
        parent::__construct($handle);
    }

}