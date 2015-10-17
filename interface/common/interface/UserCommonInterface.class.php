<?php

class UserCommonInterface extends BaseInterface {

    public static function getList($params) {
        return UcUserInterface::getList($params);
    }

    public static function getCount($params = array()) {
        return UcUserInterface::getCount($params);
    }

    public static function getById($params) {
        return UcUserInterface::getById($params);
    }

    public static function save($params) {
        return UcUserInterface::save($params);
    }

    public static function getLoginUserInfo() {
        return UcUserInterface::getLoginUserInfo();
    }

    public static function getByLoginName($params) {
        return UcUserInterface::getByLoginName($params);
    }

    public static function encryptPassword($params) {
        return UcUserInterface::encryptPassword($params);
    }

    public static function logout() {
        UcUserInterface::logout();
    }

}