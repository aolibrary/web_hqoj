<?php

require_once __DIR__ . '/../logic/LoggerLogic.class.php';

class LoggerInterface extends BaseInterface {

    public static function getList($params = array()) {
        $field  = self::get('field', $params, '*', TYPE_STR_Y);
        $where  = self::get('where', $params, array(), TYPE_ARR);
        $order  = self::get('order', $params, array(), TYPE_ARR);
        $limit  = self::get('limit', $params, -1, TYPE_INT);
        $offset = self::get('offset', $params, 0, TYPE_INT);
        return LoggerLogic::getList($field, $where, $order, $limit, $offset);
    }

    public static function getCount($params = array()) {
        $where  = $params;
        return LoggerLogic::getCount($where);
    }

    public static function getRow($params) {
        $field  = self::get('field', $params, '*', TYPE_STR_Y);
        $where  = self::get('where', $params, array(), TYPE_ARR);
        $order  = self::get('order', $params, array(), TYPE_ARR);
        $offset = self::get('offset', $params, 0, TYPE_INT);
        return LoggerLogic::getRow($field, $where, $order, $offset);
    }

    public static function getById($params) {
        $id     = self::get('id', $params, 0, TYPE_INT_GT0|TYPE_ARR_Y, true);
        return LoggerLogic::getById($id);
    }

    public static function getByField($params) {
        $field = key($params);
        self::check($field, TYPE_STR_Y);
        $value = self::get($field, $params, '', TYPE_NUM|TYPE_STR|TYPE_ARR_Y, true);
        return LoggerLogic::getByField($field, $value);
    }

    /**
     * 通用添加和编辑接口，如果id不传，或者id=0，那么为插入新数据
     *
     * @param   array   $params 通过judgeExist查看必传参数，通过initFromArray查看有效参数
     * @return  int     如果是新增，返回id；如果是更新，返回affected_rows
     * @throws  LibraryException
     */
    public static function save($params = array()) {

        // 获取id，非必传
        $id = self::get('id', $params, 0, TYPE_INT);

        if ($id == 0) {

            // 插入数据必传参数校验
            self::judge($params, array(

            ));

            // 插入数据过滤
            $data = self::getAll($params, array(
                'create_time' => TYPE_INT,    //
                'update_time' => TYPE_INT,    //
                'tag'         => TYPE_STR,    // 标签
                'level'       => TYPE_INT,    // 级别
                'client_ip'   => TYPE_INT,    // 客户的ip
                'client_port' => TYPE_INT,    // 客户的端口
                'server_ip'   => TYPE_INT,    // 服务的ip
                'server_port' => TYPE_INT,    // 服务的端口
                'url'         => TYPE_STR,    // 请求的url
                'post'        => TYPE_STR,    // 请求的post参数
                'loc'         => TYPE_STR,    // 日志发生的文件位置
                'message'     => TYPE_STR,    // 日志内容
                'trace'       => TYPE_STR,    // 堆栈信息
            ));

        } else {

            // 允许更新的字段
            $data = self::getAll($params, array(

            ));

        }

        return LoggerLogic::save($data, $id);
    }

    /**
     * 通用删除接口，如果不是物理删除，记得修改Model中的其他接口，建议物理删除
     *
     * @param   array   $params id
     * @return  int     affected_rows
     * @throws  LibraryException
     */
    public static function deleteById($params = array()) {
        $id     = self::get('id', $params, 0, TYPE_INT_GT0, true);
        return LoggerLogic::deleteById($id);
    }

}