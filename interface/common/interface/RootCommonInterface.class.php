<?php

class RootCommonInterface extends BaseInterface {

    /**
     * 权限控制
     *
     * @param   $params array(
     *              'user_id',  // 用户id
     *              'path',     // 权限路径
     *          )
     * @return  bool
     * @throws  LibraryException
     */
    public static function allowed($params) {

        $userId = self::get('user_id', $params, 0, TYPE_INT_GT0, true);
        $path   = self::get('path', $params, '', TYPE_STR_Y, true);

        if (empty($path)) {
            Logger::warn('interface', '权限校验时，传入了空权限，系统默认返回true！');
            return true;
        }

        // 校验权限是否存在
        $existed = RootPermissionInterface::findPath(array(
            'path'          => $path,
            'from_cache'    => true,
        ));
        if (!$existed) {
            Logger::warn('interface', "权限{$path}不存在！");
            return false;
        }

        $managerId = RootManagerInterface::getEnabledId(array(
            'user_id'       => $userId,
            'from_cache'    => true,
        ));

        if (empty($managerId)) {
            return false;
        }

        $allowed = RootManagerInterface::checkPermission(array(
            'id'            => $managerId,
            'path'          => $path,
            'from_cache'    => true,
        ));
        return empty($allowed) ? false : true;
    }

}