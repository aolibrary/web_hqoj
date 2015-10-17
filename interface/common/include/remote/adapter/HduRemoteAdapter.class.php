<?php

class HduRemoteAdapter {

    public static function getProblemInfo($problemId) {

        // 新建一个curl
        $ch = new Curl();
        $url = 'http://acm.hdu.edu.cn/showproblem.php?pid=' . $problemId;
        $html = $ch->get($url);
        if (empty($html) || $ch->error()) {
            $ch->close();
            return false;
        }
        $ch->close();

        $problemInfo = array();
        $matches = array();

        // 获取标题
        preg_match('/<td align=center><h1 style=\'color:#1A5CC8\'>(.*)<\/h1>/sU', $html, $matches);
        $problemInfo['title'] = '';
        if (!empty($matches[1])) {
            $problemInfo['title'] = trim($matches[1]);
            $problemInfo['title'] = iconv('GBK', 'UTF-8', $problemInfo['title']);
        }

        // 获取来源
        preg_match('/>Source.*<a.*\/search.php.*>(.*)<\/a>/sU', $html, $matches);
        $problemInfo['source'] = '';
        if (!empty($matches[1])) {
            $problemInfo['source'] = trim($matches[1]);
            $problemInfo['source'] = iconv('GBK', 'UTF-8', $problemInfo['source']);
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