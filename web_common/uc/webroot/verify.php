<?php

require_once __DIR__ . '/../../../library/bootstrap.php';

$verify = new Verify();
Session::set('check_code', $verify->getCode());
$verify->output();

