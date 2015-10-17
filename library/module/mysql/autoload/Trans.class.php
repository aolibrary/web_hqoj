<?php

class Trans extends MysqliClient {

    // 标记是否在事务中
    public $inTrans = false;

    public function __construct($server) {

        parent::__construct($server, true);
    }

    // 防止克隆
    private function __clone() {}

    public function __destruct() {

        // 如果是支持事务的，关闭临时连接
        if ($this->inTrans) {
            // 如果在事务中，Mysql close后，事务不会提交
            //$this->mysqli->rollback();
            // 添加日志
            Logger::fatal('library', '事务中断！');
            trigger_error('事务中断！', E_USER_WARNING);
        }
        $this->mysqli->close();
    }

    public function begin() {
        if ($this->inTrans) {
            throw new LibraryException('同一个连接下的事务不能嵌套！');
        }
        $ret = $this->mysqli->begin_transaction();
        if (false === $ret) {
            $msg = '开启事务失败！' . $this->mysqli->error;
            Logger::fatal('mysql', $msg);
            throw new LibraryException($msg);
        }
        $this->inTrans = true;
        return $ret;
    }

    public function commit() {
        if (! $this->inTrans) {
            throw new LibraryException('当前连接没有开启事务，无法提交！');
        }
        $ret = $this->mysqli->commit();
        if (false === $ret) {
            $this->rollback();
            $msg = '提交事务失败！' . $this->mysqli->error;
            Logger::fatal('mysql', $msg);
            throw new LibraryException($msg);
        }
        $this->inTrans = false;
        return $ret;
    }

    public function rollback() {
        if (! $this->inTrans) {
            throw new LibraryException('当前连接没有开启事务，无法回滚！');
        }
        // 回滚失败可能造成mysql锁死
        $ret = $this->mysqli->rollback();
        if (false === $ret) {
            $msg = '回滚事务失败！' . $this->mysqli->error;
            Logger::fatal('mysql', $msg);
            throw new LibraryException($msg);
        }
        $this->inTrans = false;
        return $ret;
    }
}