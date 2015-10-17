<?php

/**
 * Class MysqliClient 对mysqli和mysqli_result的基本方法进行封装
 * @notice  1. 如果需要使用事务，那么用new来新建MysqliClient对象，2. 如果不需要，那么用getInstance来新建对象
 */

class MysqliClient {

    // mysqli临时连接
    public $mysqli  = null;

    // DbConfig中的服务器配置
    public $server  = array();

    public function __construct($server, $isTrans = false) {

        if ($isTrans) {
            $this->mysqli = MysqliPool::getMysqli($server, true);
        } else {
            $this->mysqli = MysqliPool::getMysqli($server);
        }
        $this->server       = $server;

        // 设置连接编码
        if (false === $this->mysqli->set_charset(DbConfig::DEFAULT_CHARSET)) {
            $mysqlError = $this->mysqli->error;
            Logger::error('mysql', $mysqlError);
            throw new LibraryException($mysqlError);
        }
    }

    /**
     * 插入操作
     *
     * @param   string  $sql
     * @return  int insert_id
     */
    public function insert($sql) {
        $this->query($sql);
        return $this->mysqli->insert_id;
    }

    /**
     * 更新操作
     *
     * @param   string  $sql
     * @return  int affected_rows
     */
    public function update($sql) {
        $this->query($sql);
        return $this->mysqli->affected_rows;
    }

    /**
     * 删除操作
     *
     * @param   string  $sql
     * @return  int affected_rows
     */
    public function delete($sql) {
        $this->query($sql);
        return $this->mysqli->affected_rows;
    }

    /**
     * 执行sql，执行失败会抛出异常
     *
     * @param   string  $sql
     * @return  mysqli_result
     * @throws  LibraryException
     */
    public function query($sql) {
        if (empty($sql)) {
            throw new LibraryException('SQL不能为空！');
        }
        $ret = $this->mysqli->query($sql);
        if (false === $ret) {
            $mysqlError = $this->mysqli->error;
            Logger::error('mysql', $mysqlError);
            throw new LibraryException($mysqlError);
        }
        Debug::p($sql);
        return $ret;
    }

    /**
     * 获取列表
     *
     * @param   string  $sql
     * @return  array   二维数组
     */
    public function queryAll($sql) {
        $result = $this->query($sql);
        $list = $result->fetch_all(MYSQLI_ASSOC);
        $result->free();
        return $list;
    }

    /**
     * 获取一行
     *
     * @param   string  $sql
     * @return  array   一维数组
     */
    public function queryRow($sql) {
        $result = $this->query($sql);
        $row = $result->fetch_assoc();
        $result->free();
        return empty($row) ? array() : $row;
    }
}