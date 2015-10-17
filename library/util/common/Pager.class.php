<?php

class Pager {

    public static function get($param = 'page') {
        return max(1, (int) Request::getGET($param, 1));
    }

}