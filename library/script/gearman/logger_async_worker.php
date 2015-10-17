<?php

require_once __DIR__ . '/../../bootstrap.php';

$worker = GearmanPool::getWorker(GearmanConfig::$SERVER_COMMON);
$worker->addFunction('logger_async', 'func');

while ($worker->work()) ;

function func (GearmanJob $job) {
    $data = json_decode($job->workload(), true);

    // 临时关闭Logger
    $tmpEnable = GlobalConfig::$LOGGER_ENABLE;
    GlobalConfig::$LOGGER_ENABLE = false;

    LoggerInterface::save($data);

    GlobalConfig::$LOGGER_ENABLE = $tmpEnable;
}
