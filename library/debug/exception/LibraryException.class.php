<?php

/**
 * Class LibraryException library抛出的异常
 */

class LibraryException extends BaseException {

    public function __construct($message, $code = 0) {

        parent::__construct($message, 'library', $code);
    }
}