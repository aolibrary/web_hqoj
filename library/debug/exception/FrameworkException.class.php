<?php

/**
 * Class FrameworkException framework抛出的异常
 */

class FrameworkException extends BaseException {

    public function __construct($message, $code = 0) {

        parent::__construct($message, 'framework', $code);
    }
}