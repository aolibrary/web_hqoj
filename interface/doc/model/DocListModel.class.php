<?php

class DocListModel extends BaseModel {

    public function __construct($handle = null) {

        $this->masterServer = DbConfig::$SERVER_MASTER;
        $this->slaveServer  = DbConfig::$SERVER_SLAVE;
        $this->dbName       = DbConfig::DB_HQOJ;
        $this->tableName    = 'doc_list';
        $this->fieldTypes   = array(
            'id'          => 'int',     // 自增id
            'create_time' => 'int',     // 创建时间
            'update_time' => 'int',     // 更新时间
            'user_id'     => 'int',     // 最后操作者
            'category'    => 'int',     // 文章分类
            'title'       => 'varchar', // 标题
            'content'     => 'text',    // 文章内容，html格式
            'hidden'      => 'int',     // 是否隐藏，0显示，1隐藏
        );
        parent::__construct($handle);
    }

}