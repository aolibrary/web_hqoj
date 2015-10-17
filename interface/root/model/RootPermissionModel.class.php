<?php

class RootPermissionModel extends BaseModel {

    public function __construct($handle = null) {

        $this->masterServer = DbConfig::$SERVER_MASTER;
        $this->slaveServer  = DbConfig::$SERVER_SLAVE;
        $this->dbName       = DbConfig::DB_HQOJ;
        $this->tableName    = 'root_permission';
        $this->fieldTypes   = array(
            'id'          => 'int',     // 权限ID
            'create_time' => 'int',     // 创建时间
            'update_time' => 'int',     // 更新时间
            'code'        => 'varchar', // 权限码
            'description' => 'varchar', // 描述
        );
        parent::__construct($handle);
    }

}