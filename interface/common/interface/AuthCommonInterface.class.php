<?php

class AuthCommonInterface extends BaseInterface {

    public static function sendEmailCode($params) {
        return UcAuthInterface::sendEmailCode($params);
    }

    public static function checkEmailCode($params) {
        return UcAuthInterface::checkEmailCode($params);
    }

    public static function deleteEmailCode($params) {
        return UcAuthInterface::deleteEmailCode($params);
    }
}