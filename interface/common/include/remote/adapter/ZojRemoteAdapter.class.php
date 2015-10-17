<?php

class ZojRemoteAdapter {

    public static function getProblemInfo($problemCode) {

        // 新建一个curl
        $ch = new Curl();
        $url = 'http://acm.zju.edu.cn/onlinejudge/showProblem.do?problemCode=' . $problemCode;
        $html = $ch->get($url);
        if (empty($html) || $ch->error()) {
            $ch->close();
            return false;
        }
        $ch->close();

        $problemInfo = array();
        $matches = array();

        // 获取标题
        preg_match('/<span class="bigProblemTitle">(.*)<\/span>/sU', $html, $matches);
        $problemInfo['title'] = '';
        if (!empty($matches[1])) {
            $problemInfo['title'] = trim($matches[1]);
        }

        // 获取来源
        preg_match('/Source:.*<strong>(.*)<\/strong>/sU', $html, $matches);
        if (empty($matches[1])) {
            preg_match('/Contest:.*<strong>(.*)<\/strong>/sU', $html, $matches);
        }
        $problemInfo['source'] = '';
        if (!empty($matches[1])) {
            $problemInfo['source'] = trim($matches[1]);
        }

        // 获取$problemId
        preg_match('/"\/onlinejudge\/submit.do\?problemId=(\d+)"/sU', $html, $matches);
        $problemInfo['problem_id'] = 0;
        if (!empty($matches[1])) {
            $problemInfo['problem_id'] = $matches[1];
        }

        $problemInfo['problem_code'] = $problemCode;
        $problemInfo['remote'] = StatusVars::REMOTE_ZOJ;

        return $problemInfo;
    }

    public static function getProblemList($from, $number) {

        $problemList = array();
        for ($i = 1; $i <= $number; $i++) {
            $problemInfo = self::getProblemInfo($from);
            $problemList[] = $problemInfo;
            $from++;
        }
        return $problemList;
    }
}