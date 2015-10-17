<?php

/**
 * 异常基类
 */

abstract class BaseException extends Exception {

    public function __construct($message, $tag, $code = 0) {

        $message = $message . ' ' . $this->getFile() . ':' . $this->getLine();
        Logger::error($tag, $message);
        parent::__construct($message, $code);
    }

}