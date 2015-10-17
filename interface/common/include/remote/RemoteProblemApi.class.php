<?php

require_once __DIR__ . '/adapter/HduRemoteAdapter.class.php';
require_once __DIR__ . '/adapter/PojRemoteAdapter.class.php';
require_once __DIR__ . '/adapter/ZojRemoteAdapter.class.php';

class RemoteProblemApi {

    /**
     * @breif  抓取题目列表，目前适用于HDU, POJ
     * @param  int $remote int 远程OJ标识
     * @param  int $from   int 开始抓取的题号
     * @param  int $number int 连续抓取的数目
     * @return array $problemList
     */
    public static function getProblemList($remote, $from, $number) {
        
        switch ($remote) {
            case StatusVars::REMOTE_HDU:
                return HduRemoteAdapter::getProblemList($from, $number);
            case StatusVars::REMOTE_POJ:
                return PojRemoteAdapter::getProblemList($from, $number);
            case StatusVars::REMOTE_ZOJ:
                return ZojRemoteAdapter::getProblemList($from, $number);
            default: break;
        }
        return array();
    }
}
