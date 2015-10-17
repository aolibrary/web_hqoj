<?php

require_once __DIR__ . '/../logic/UcUserLogic.class.php';

class UcUserInterface extends BaseInterface {

    public static function getList($params = array()) {
        $field  = self::get('field', $params, '*', TYPE_STR_Y);
        $where  = self::get('where', $params, array(), TYPE_ARR);
        $order  = self::get('order', $params, array(), TYPE_ARR);
        $limit  = self::get('limit', $params, -1, TYPE_INT);
        $offset = self::get('offset', $params, 0, TYPE_INT);
        return UcUserLogic::getList($field, $where, $order, $limit, $offset);
    }

    public static function getCount($params = array()) {
        $where  = $params;
        return UcUserLogic::getCount($where);
    }

    public static function getRow($params) {
        $field  = self::get('field', $params, '*', TYPE_STR_Y);
        $where  = self::get('where', $params, array(), TYPE_ARR);
        $order  = self::get('order', $params, array(), TYPE_ARR);
        $offset = self::get('offset', $params, 0, TYPE_INT);
        return UcUserLogic::getRow($field, $where, $order, $offset);
    }

    public static function getById($params) {
        $id     = self::get('id', $params, 0, TYPE_INT_GT0|TYPE_ARR, true);
        return UcUserLogic::getById($id);
    }

    public static function getByField($params) {
        $field = key($params);
        self::check($field, TYPE_STR_Y);
        $value = self::get($field, $params, '', TYPE_NUM|TYPE_STR|TYPE_ARR, true);
        return UcUserLogic::getByField($field, $value);
    }

    /**
     * 通用添加和编辑接口，如果id不传，或者id=0，那么为插入新数据
     *
     * @param   array   $params 参数列表，通过judgeExist查看必传参数，通过initFromArray查看有效参数
     * @return  int     如果是新增，返回id；如果是更新，返回affected_rows
     * @throws  LibraryException
     */
    public static function save($params = array()) {

        // 获取id，非必传
        $id    = self::get('id', $params, 0, TYPE_INT);
        $trans = self::get('trans', $params, null, TYPE_OBJ);

        if (0 == $id) {

            // 必传字段
            self::judge($params, array(
                'username',
                'password',
            ));

            // 允许插入的字段
            $data = self::getAll($params, array(
                'username'  => TYPE_STR_Y,    // 用户名
                'password'  => TYPE_STR_Y,    // 用户密码，明文
                'nickname'  => TYPE_STR_Y,    // 昵称
                'reg_ip'    => TYPE_INT,      // 注册的Ip
            ));

        } else {

            // 允许更新的字段
            $data = self::getAll($params, array(
                'password'      => TYPE_STR_Y,    // 用户密码，明文
                'head_img'      => TYPE_STR_Y,    // 头像
                'nickname'      => TYPE_STR_Y,
                'motto'         => TYPE_STR_Y,
                'sex'           => TYPE_INT,
                'share'         => TYPE_INT,
                'email'         => TYPE_STR_Y,
                'submit_all'    => TYPE_INT,
                'solved_all'    => TYPE_INT,
                'solved_hqu'    => TYPE_INT,
                'solved_hdu'    => TYPE_INT,
                'solved_poj'    => TYPE_INT,
                'solved_zoj'    => TYPE_INT,
            ));

        }

        return UcUserLogic::save($data, $id, $trans);
    }

    /**
     * 通用删除接口，如果不是物理删除，记得修改Model中的其他接口，建议物理删除
     *
     * @param   array   $params
     * @return  int     affected_rows
     * @throws  LibraryException
     */
    public static function deleteById($params) {
        $id = self::get('id', $params, 0, TYPE_INT_GT0, true);
        return UcUserLogic::deleteById($id);
    }

    /**
     * 登陆并获取用户信息
     *
     * @param   array   $params
     * @return  array   array( 'ret', 'msg', 'user_info' )，登陆结果，返回消息，用户信息
     * @throws  LibraryException
     */
    public static function login($params) {

        // 可以是username, email, telephone
        $loginName  = self::get('login_name', $params, '', TYPE_STR_Y, true);
        $password   = self::get('password', $params, '', TYPE_STR_Y, true);
        return UcUserLogic::login($loginName, $password);
    }

    /**
     * 注销账户
     */
    public static function logout() {
        UcUserLogic::logout();
    }

    /**
     * 获取登陆用户信息
     *
     * @return  array   如果没有登陆，返回array()
     */
    public static function getLoginUserInfo() {
        return UcUserLogic::getLoginUserInfo();
    }

    /**
     * 根据用户名，邮箱，手机获取用户信息
     *
     * @param   $params
     * @return  array   用户信息
     */
    public static function getByLoginName($params) {
        $loginName  = self::get('login_name', $params, '', TYPE_STR_Y, true);
        return UcUserLogic::getByLoginName($loginName);
    }

    /**
     * 对密码进行加密
     *
     * @param   $params
     * @return  string
     * @throws  LibraryException
     */
    public static function encryptPassword($params) {
        $password = self::get('password', $params, '', TYPE_STR_Y, true);
        return UcUserLogic::encryptPassword($password);
    }

}