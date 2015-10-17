<?php

require_once __DIR__ . '/../../bootstrap.php';

$worker = GearmanPool::getWorker(GearmanConfig::$SERVER_COMMON);
$worker->addFunction('email_async', 'func');

while ($worker->work()) ;

function func (GearmanJob $job) {
    $params = json_decode($job->workload(), true);

    $config  = Arr::get('config', $params, array());
    $subject = Arr::get('subject', $params, '');
    $body    = Arr::get('body', $params, '');
    $to      = Arr::get('to', $params, '');
    $cc      = Arr::get('cc', $params, '');
    $bcc     = Arr::get('bcc', $params, '');

    $email = new EmailClient($config);
    $email->send($subject, $body, $to, $cc, $bcc);

}
