<?php

/**
 * 从各大oj同步数据到oj_solution表
 *
 * @notice  相同帐号在同一个时间提交代码，极有可能获取结果时混淆
 */

require_once __DIR__ . '/../../../bootstrap.php';
require_once INCLUDE_PATH . '/remote_judge/HduJudger.class.php';
require_once INCLUDE_PATH . '/remote_judge/PojJudger.class.php';
require_once INCLUDE_PATH . '/remote_judge/ZojJudger.class.php';

// 获取命令行参数
$solutionId = $argv[1];
$solutionInfo = OjSolutionInterface::getById(array(
    'id'    => $solutionId,
));
if (empty($solutionInfo)) {
    Logger::error('judge', "Solution不存在！solutionId={$solutionId}");
    exit(1);
}

// 并发的客户端数量
const MAX_HDU_RUNNING = 2;
const MAX_POJ_RUNNING = 2;
const MAX_ZOJ_RUNNING = 2;

try {

    if ($solutionInfo['remote'] == StatusVars::REMOTE_HDU) {

        if (empty($solutionInfo['run_id']) || $solutionInfo['remote_uid'] == -1) {
            $uid = $solutionId%MAX_HDU_RUNNING;
            $judge = new HduJudger($solutionId, $uid);
            $judge->run();
        } else {
            $uid = $solutionInfo['remote_uid'];
            $judge = new HduJudger($solutionId, $uid);
            $judge->sync();
        }

    } else if ($solutionInfo['remote'] == StatusVars::REMOTE_POJ) {

        if (empty($solutionInfo['run_id']) || $solutionInfo['remote_uid'] == -1) {
            $uid = $solutionId%MAX_POJ_RUNNING;
            $judge = new PojJudger($solutionId, $uid);
            $judge->run();
        } else {
            $uid = $solutionInfo['remote_uid'];
            $judge = new PojJudger($solutionId, $uid);
            $judge->sync();
        }

    } else if ($solutionInfo['remote'] == StatusVars::REMOTE_ZOJ) {

        if (empty($solutionInfo['run_id']) || $solutionInfo['remote_uid'] == -1) {
            $uid = $solutionId%MAX_ZOJ_RUNNING;
            $judge = new ZojJudger($solutionId, $uid);
            $judge->run();
        } else {
            $uid = $solutionInfo['remote_uid'];
            $judge = new ZojJudger($solutionId, $uid);
            $judge->sync();
        }

    }

} catch (Exception $e) {
    Logger::error('judge', $e->getMessage());
    throw $e;
}

