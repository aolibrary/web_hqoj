<?php

class OjSolutionHelper {

    /**
     * 计算题库实时状态；返回level（solution的权限）和permission（访问许可，动态计算的结果）
     *
     * @param   array   $solutionInfo
     * @param   int     $userShare      solution所有者是否允许共享
     * @param   int     $loginUserId    当前登录的用户
     * @param   bool    $isOjAdmin      是否是管理员
     * @return  array
     */
    public static function solutionPermission($solutionInfo, $userShare, $loginUserId, $isOjAdmin) {

        $loginUserId = intval($loginUserId);

        // 先计算level
        if ($solutionInfo['contest_id'] > 0) {
            // 竞赛不显示
            $level = StatusVars::SOLUTION_PRIVATE;
        } else if ($solutionInfo['share']) {
            $level = StatusVars::SOLUTION_SHARE;
        } else if ($userShare) {
            $level = StatusVars::SOLUTION_PUBLIC;
        } else {
            $level = StatusVars::SOLUTION_PROTECTED;
        }

        // 在计算permission
        if ($isOjAdmin || $solutionInfo['user_id'] == $loginUserId
            || in_array($level, array(StatusVars::SOLUTION_PUBLIC, StatusVars::SOLUTION_SHARE))) {
            $permission = true;
        } else {
            $permission = false;
        }

        return array($level, $permission);
    }

    /**
     * 判断一个solution是否有产生日志
     *
     * @param   array   $solutionInfo
     * @return  bool
     */
    public static function hasLog($solutionInfo) {

        $hasLog = false;
        if ($solutionInfo['remote'] == StatusVars::REMOTE_HQU || in_array($solutionInfo['result'], array(StatusVars::COMPILATION_ERROR, StatusVars::RUNTIME_ERROR))) {
            $hasLog = true;
        }
        if ($solutionInfo['remote'] == StatusVars::REMOTE_POJ && $solutionInfo['result'] == StatusVars::RUNTIME_ERROR) {
            $hasLog = false;
        }
        return $hasLog;
    }

}