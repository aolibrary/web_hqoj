<?php

class OjContestApplyModel extends BaseModel {

    public function __construct($handle = null) {

        $this->masterServer = DbConfig::$SERVER_MASTER;
        $this->slaveServer  = DbConfig::$SERVER_SLAVE;
        $this->dbName       = DbConfig::DB_HQOJ;
        $this->tableName    = 'oj_contest_apply';
        $this->fieldTypes   = array(
            'id'          => 'int',     // 报名ID
            'create_time' => 'int',     // 报名时间
            'update_time' => 'int',     // 更新时间
            'contest_id'  => 'int',     // 竞赛ID
            'user_id'     => 'int',     // 申请参赛的用户
            'real_name'   => 'varchar', // 姓名
            'sex'         => 'int',     // 性别
            'xuehao'      => 'varchar', // 学号
            'xueyuan'     => 'int',     // 学院
            'status'      => 'int',     // 当前报名状态
            'is_diy'      => 'int',     // 是否是DIY比赛
        );
        parent::__construct($handle);
    }

}