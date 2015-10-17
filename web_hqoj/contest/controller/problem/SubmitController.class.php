<?php

class SubmitController extends ProjectController {

    public function defaultAction() {

        // 获取参数
        $problemHash = Request::getGET('problem-hash');

        $globalId = array_search($problemHash, $this->contestInfo['problem_hash'], true);
        if (empty($globalId)) {
            $this->renderError('竞赛中无此题！');
        }

        // 校验题目
        $problemInfo = OjProblemInterface::getById(array('id' => $globalId));
        if (empty($problemInfo)) {
            $this->renderError('题目不存在！');
        }

        $this->renderFramework(array(
            'problemInfo' => $problemInfo,
        ), 'problem/submit.php');
    }

    public function ajaxSubmitAction() {

        // 获取参数
        $language    = (int) Request::getPOST('language');
        $code        = Request::getPOST('code', '', true);
        $problemHash = Request::getPOST('problem-hash');
        $userId      = $this->loginUserInfo['id'];

        $globalId = array_search($problemHash, $this->contestInfo['problem_hash']);
        if (empty($globalId)) {
            $this->renderError('竞赛中无此题！');
        }

        // 校验
        if (strlen($code) < 50 || strlen($code) > 65535) {
            $this->renderError('代码长度超出范围，请限制为50-65535(BYTE)！');
        }
        $problemInfo = OjProblemInterface::getById(array('id' => $globalId));
        if (empty($problemInfo) || $problemInfo['hidden']) {
            $this->renderError('题目不存在！');
        }
        if (!array_key_exists($language, StatusVars::$LANGUAGE_SUPPORT[$problemInfo['remote']])) {
            $this->renderError('编译器不支持！');
        }
        if (OjSolutionInterface::submitAlready(array('user_id' => $userId))) {
            $this->renderError('提交频繁！');
        }

        // 非法字符判断
        if ($problemInfo['remote'] == StatusVars::REMOTE_HDU) {
            if (false === iconv('UTF-8', 'GBK', $code)) {
                $this->renderError('代码中存在非法字符！');
            }
        }

        OjSolutionInterface::save(array(
            'global_id'     => $globalId,
            'user_id'       => $userId,
            'language'      => $language,
            'source'        => $code,
            'contest_id'    => $this->contestInfo['id'],
        ));
        $this->renderAjax(0);
    }
}