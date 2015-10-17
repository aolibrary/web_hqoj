<?php

/**
 * Class InterfaceException 接口层抛出的异常
 */

class InterfaceException extends BaseException {

    public function __construct($message, $code = 0) {

        parent::__construct($message, 'interface', $code);
    }
}