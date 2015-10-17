<?php

class PojRemoteAdapter {

    public static function getProblemInfo($problemId) {

        // 新建一个curl
        $ch = new Curl();
        $url = 'http://poj.org/problem?id=' . $problemId;
        $html = $ch->get($url);
        if (empty($html) || $ch->error()) {
            $ch->close();
            return false;
        }
        $ch->close();

        $problemInfo = array();
        $matches = array();

        // 获取标题
        preg_match('/<div class="ptt" lang="en-US">(.*)<\/div>/sU', $html, $matches);
        $problemInfo['title'] = '';
        if (!empty($matches[1])) {
            $problemInfo['title'] = trim($matches[1]);
        }

        // 获取来源
        preg_match('/<a href="searchproblem\?field=source.*>(.*)<\/a>/sU', $html, $matches);
        $problemInfo['source'] = '';
        if (!empty($matches[1])) {
            $problemInfo['source'] = trim($matches[1]);
        }

        $problemInfo['problem_id'] = $problemId;
        $problemInfo['problem_code'] = $problemId;

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