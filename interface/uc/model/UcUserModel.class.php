<?php

class UcUserModel extends BaseModel {

    public function __construct($handle = null) {

        $this->masterServer = DbConfig::$SERVER_MASTER;
        $this->slaveServer  = DbConfig::$SERVER_SLAVE;
        $this->dbName       = DbConfig::DB_HQOJ;
        $this->tableName    = 'uc_user';
        $this->fieldTypes   = array(
            'id'          => 'int',     // 用户id，自增
            'create_time' => 'int',     // 创建时间
            'update_time' => 'int',     // 更新时间
            'username'    => 'varchar', // 用户名
            'password'    => 'varchar', // 用户密码
            'head_img'    => 'varchar', // 头像
            'nickname'    => 'varchar', // 昵称
            'sex'         => 'int',     // 1男生，2女生
            'email'       => 'varchar', // 邮箱
            'telephone'   => 'varchar', // 电话号码
            'motto'       => 'varchar', // 个性签名
            'level'       => 'int',     // 等级
            'share'       => 'int',     // 是否共享代码
            'reg_ip'      => 'bigint',  // 注册ip
            'submit_all'  => 'int',     // 
            'solved_all'  => 'int',     // 
            'solved_hqu'  => 'int',     // 
            'solved_hdu'  => 'int',     // 
            'solved_poj'  => 'int',     // 
            'solved_zoj'  => 'int',     // 
        );
        parent::__construct($handle);
    }

}