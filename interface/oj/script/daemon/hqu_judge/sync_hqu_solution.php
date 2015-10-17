<?php

/**
 * 同步当前的solution
 */

require_once __DIR__ . '/../../../bootstrap.php';

// 获取命令行参数
$solutionId = $argv[1];
if (empty($solutionId)) {
    Logger::error('judge', '缺少参数：$solutionId');
    exit(1);
}

// 从评判队列中获取该条信息
$queueInfo = OjJudgeInterface::getRow(array(
    'where' => array(
        array('solution_id', '=', $solutionId),
    ),
));
if (empty($queueInfo)) {
    Logger::error('judge', "评判队列中不存在Solution！solutionId={$solutionId}");
    exit(1);
}

$trans = new Trans(DbConfig::$SERVER_TRANS);
$trans->begin();

// 保存result
try {
    OjSolutionInterface::save(array(
        'id'            => $solutionId,
        'time_cost'     => $queueInfo['time_cost'],
        'memory_cost'   => $queueInfo['memory_cost'],
        'judge_time'    => $queueInfo['judge_time'],
        'run_id'        => $queueInfo['id'],
        'result'        => $queueInfo['result'],
        'trans'         => $trans,
    ));
} catch (Exception $e) {
    Logger::error('judge', "solutionId={$solutionId}，保存solution失败，" . $e->getMessage());
    $trans->rollback();
    throw $e;
}

// 保存log
try {
    OjSolutionLogInterface::save(array(
        'trans'       => $trans,
        'solution_id' => $solutionId,
        'ce'          => $queueInfo['ce'],
        're'          => $queueInfo['re'],
        'detail'      => $queueInfo['detail'],
    ));
} catch (Exception $e) {
    Logger::error('judge', "solutionId={$solutionId}，保存log失败，" . $e->getMessage());
    $trans->rollback();
    throw $e;
}

$trans->commit();
exit(0);

