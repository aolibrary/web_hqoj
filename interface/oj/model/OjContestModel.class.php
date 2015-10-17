<?php

class OjContestModel extends BaseModel {

    public function __construct($handle = null) {

        $this->masterServer = DbConfig::$SERVER_MASTER;
        $this->slaveServer  = DbConfig::$SERVER_SLAVE;
        $this->dbName       = DbConfig::DB_HQOJ;
        $this->tableName    = 'oj_contest';
        $this->fieldTypes   = array(
            'id'             => 'int',     // 竞赛ID，自增
            'create_time'    => 'int',     // 创建时间
            'update_time'    => 'int',     // 更新时间
            'user_id'        => 'int',     // 创建竞赛的管理员
            'title'          => 'varchar', // 竞赛标题
            'description'    => 'text',    // 竞赛的相关描述，比如奖品啦
            'type'           => 'int',     // 竞赛类型：公开，报名，密码
            'password'       => 'varchar', // 比赛密码，前提是密码类型的比赛
            'begin_time'     => 'int',     // 开始时间
            'end_time'       => 'int',     // 结束时间
            'notice'         => 'varchar', // 比赛消息提醒
            'problem_json'   => 'varchar', // 题目信息，json数据
            'hidden'         => 'int',     // 是否隐藏
            'is_diy'         => 'int',     // 是否是DIY比赛
            'is_active'      => 'int',     // 是否已激活，有人提交就激活
            'problem_hidden' => 'int',     // 是否隐藏题目
        );
        parent::__construct($handle);
    }

}