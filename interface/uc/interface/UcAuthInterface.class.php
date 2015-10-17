<?php

require_once __DIR__ . '/../logic/UcAuthLogic.class.php';

class UcAuthInterface extends BaseInterface {

    /**
     * 发送邮箱验证码
     *
     * @param   $params
     * @return  bool|string     成功返回验证码，失败返回false
     * @throws  LibraryException
     */
    public static function sendEmailCode($params) {
        $email    = self::get('email', $params, '', TYPE_STR_Y, true);
        $repeatAt = self::get('repeat_at', $params, time()+60, TYPE_INT, true);
        return UcAuthLogic::sendEmailCode($email, $repeatAt);
    }

    /**
     * 校验邮箱验证码
     *
     * @param   $params
     * @return  bool
     * @throws  LibraryException
     */
    public static function checkEmailCode($params) {
        $email = self::get('email', $params, '', TYPE_STR_Y, true);
        $code  = self::get('code', $params, '', TYPE_STR_Y, true);
        return UcAuthLogic::checkEmailCode($email, $code);
    }

    /**
     * 删除指定邮箱验证码缓存
     *
     * @param   $params
     * @return  bool
     * @throws  LibraryException
     */
    public static function deleteEmailCode($params) {
        $email = self::get('email', $params, '', TYPE_STR_Y, true);
        return UcAuthLogic::deleteEmailCode($email);
    }

    /**
     * 重置用户密码时，创建一条resetTicket
     *
     * @param   $params
     * @return  string  $resetTicket
     * @throws  LibraryException
     */
    public static function makeResetTicket($params) {
        $loginName = self::get('login_name', $params, '', TYPE_STR_Y, true);
        return UcAuthLogic::makeResetTicket($loginName);
    }

    /**
     * 根据resetTicket获取用户信息，如果resetTicket失效，那么获取不到用户信息
     *
     * @param   $params
     * @return  array
     * @throws  LibraryException
     */
    public static function getUserInfoByResetTicket($params) {
        $resetTicket = self::get('reset_ticket', $params, '', TYPE_STR_Y, true);
        return UcAuthLogic::getUserInfoByResetTicket($resetTicket);
    }

    /**
     * 清空resetTicket缓存
     *
     * @param   $params
     * @return  bool
     * @throws  LibraryException
     */
    public static function deleteResetTicket($params) {
        $resetTicket = self::get('reset_ticket', $params, '', TYPE_STR_Y, true);
        return UcAuthLogic::deleteResetTicket($resetTicket);
    }

}