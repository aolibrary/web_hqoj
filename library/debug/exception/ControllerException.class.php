<?php

/**
 * Class ControllerException 接口层抛出的异常
 */

class ControllerException extends BaseException {

    public function __construct($message, $code = 0) {

        parent::__construct($message, 'controller', $code);
    }
}