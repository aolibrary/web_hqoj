<?php

class TplModel extends BaseModel {

    public function __construct($handle = null) {

        $this->masterServer = DbConfig::$SERVER_MASTER;
        $this->slaveServer  = DbConfig::$SERVER_SLAVE;
        $this->dbName       = DbConfig::DB_COMMON;
        $this->tableName    = '{$tableName}';
        $this->fieldTypes   = array(
            '{$fieldTypes}'
        );
        parent::__construct($handle);
    }

}