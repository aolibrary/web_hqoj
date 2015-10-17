<?php

class OjCommonHelper {

    public static function getHeadUrl($cdnKey, $sex) {

        if (empty($cdnKey)) {
            return $sex == UcUserModelVars::SEX_FEMALE ?
                '//sta.hqoj.net/image/www/oj/head/default-girl.jpg' :
                '//sta.hqoj.net/image/www/oj/head/default-boy.jpg';
        }
        return Cdn::getUrl($cdnKey);
    }

    public static function getSourceUrls($problemInfo) {

        if (empty($problemInfo['source'])) {
            return '';
        }
        $list = explode(',', $problemInfo['source']);
        $retList = array();
        foreach ($list as &$str) {
            $str = trim($str);
            $retList[] = sprintf('<a href="/problem_list/?remote=%d&search-type=2&keyword=%s">%s</a>', $problemInfo['remote'], $str, $str);
        }

        return implode(', ', $retList);
    }

    public static function getColorName($userInfo) {

        // level1，男生红色铭牌，女生粉色
        if ($userInfo['level'] == 1) {
            if ($userInfo['sex'] == 2) {
                return '<span style="padding: 3px 5px; color: #FFF; background-color: #FCA7FC;">' . $userInfo['nickname'] . '</span>';
            } else {
                return '<span style="padding: 3px 5px; color: #FFF; background-color: #E53E38;">' . $userInfo['nickname'] . '</span>';
            }
        }
        // level2, 金色铭牌
        if ($userInfo['level'] == 2) {
            return '<span style=" padding: 3px 5px; color: #AA5B06; background-color: #FDC03D;">' . $userInfo['nickname'] . '</span>';
        }
        // 紫色昵称
        if ($userInfo['solved_all'] >= 800) {
            return '<span style="color: #AA10CC;">' . $userInfo['nickname'] . '</span>';
        }
        // 红色昵称
        if ($userInfo['solved_all'] >= 300) {
            return '<span style="color: #E53E38;">' . $userInfo['nickname'] . '</span>';
        }
        // 绿色昵称
        if ($userInfo['solved_all'] >= 10) {
            return '<span style="color: #4DB849;">' . $userInfo['nickname'] . '</span>';
        }
        return '<span style="color: #333;">' . $userInfo['nickname'] . '</span>';
    }

    public static function getStatusUrl($username, $remote, $problemCode, $result) {

        $url = Url::make('/status_list/', array(
            'username'      => $username,
            'remote'        => $remote,
            'problem-code'  => $problemCode,
            'result'        => $result,
        ));
        return $url;
    }

    public static function getSrcUrl($remote, $problemId, $problemCode) {

        if (empty($problemId) || empty($problemCode) || empty($remote)) {
            return false;
        }
        $srcUrl = array();
        switch ($remote) {
            case StatusVars::REMOTE_HDU:
                $srcUrl['problem_url']  = 'http://acm.hdu.edu.cn/showproblem.php?pid=' . $problemId;
                $srcUrl['statis_url']   = 'http://acm.hdu.edu.cn/statistic.php?pid=' . $problemId;
                $srcUrl['status_url']   = 'http://acm.hdu.edu.cn/status.php?pid=' . $problemId;
                break;
            case StatusVars::REMOTE_POJ:
                $srcUrl['problem_url']  = 'http://poj.org/problem?id=' . $problemId;
                $srcUrl['statis_url']   = 'http://poj.org/problemstatus?problem_id=' . $problemId;
                $srcUrl['status_url']   = 'http://poj.org/status?problem_id=' . $problemId;
                break;
            case StatusVars::REMOTE_ZOJ:
                $srcUrl['problem_url']  = 'http://acm.zju.edu.cn/onlinejudge/showProblem.do?problemCode=' . $problemCode;
                $srcUrl['statis_url']   = 'http://acm.zju.edu.cn/onlinejudge/showProblemStatus.do?problemId=' . $problemId;
                $srcUrl['status_url']   = 'http://acm.zju.edu.cn/onlinejudge/showRuns.do?contestId=1&problemCode=' . $problemCode;
                break;
            default:
                break;
        }
        return $srcUrl;
    }
}