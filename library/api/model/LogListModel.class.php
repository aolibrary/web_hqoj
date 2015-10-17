<?php

class LogListModel extends BaseModel {

    public function __construct($handle = null) {

        $this->masterServer = DbConfig::$SERVER_MASTER;
        $this->slaveServer  = DbConfig::$SERVER_SLAVE;
        $this->dbName       = DbConfig::DB_LOG;
        $this->tableName    = 'log_list';
        $this->fieldTypes   = array(
            'id'          => 'int',     // 
            'create_time' => 'int',     // 
            'update_time' => 'int',     // 
            'tag'         => 'varchar', // 标签
            'level'       => 'int',     // 级别
            'client_ip'   => 'bigint',  // 客户的ip
            'client_port' => 'int',     // 客户的端口
            'server_ip'   => 'bigint',  // 服务的ip
            'server_port' => 'int',     // 服务的端口
            'url'         => 'varchar', // 请求的url
            'post'        => 'varchar', // 请求的post参数
            'loc'         => 'varchar', // 日志发生的文件位置
            'message'     => 'varchar', // 日志内容
            'trace'       => 'text',    // 堆栈信息
        );
        parent::__construct($handle);
    }

}