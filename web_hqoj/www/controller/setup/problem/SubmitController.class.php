<?php

class SubmitController extends ProjectBackendController {

    public function defaultAction() {

        // 获取参数
        $globalId = (int) Request::getGET('global-id');
        if (empty($globalId)) {
            $this->renderError();
        }

        // 校验题目
        $problemInfo = OjProblemInterface::getById(array('id' => $globalId));
        if (empty($problemInfo) || $problemInfo['user_id'] != $this->loginUserInfo['id']) {
            $this->renderError('题目不存在！');
        }

        $this->renderFramework(array(
            'problemInfo' => $problemInfo,
        ), 'setup/problem/submit.php');
    }

    public function ajaxSubmitAction() {

        // 获取参数
        $globalId   = Request::getPOST('global-id');
        $language   = Request::getPOST('language');
        $code       = Request::getPOST('code', '', true);

        // 校验
        if (strlen($code) < 50 || strlen($code) > 65535) {
            $this->renderError('代码长度超出范围，请限制为50-65535(BYTE)！');
        }
        $problemInfo = OjProblemInterface::getById(array('id' => $globalId));
        if (empty($problemInfo)) {
            $this->renderError('题目不存在！');
        }
        if ($problemInfo['user_id'] != $this->loginUserInfo['id']) {
            $this->renderError('你没有权限提交代码！');
        }
        if (!array_key_exists($language, StatusVars::$LANGUAGE_SUPPORT[$problemInfo['remote']])) {
            $this->renderError('编译器不支持！');
        }

        // 非法字符判断
        if ($problemInfo['remote'] == StatusVars::REMOTE_HDU) {
            if (false === iconv('UTF-8', 'GBK', $code)) {
                $this->renderError('代码中存在非法字符！');
            }
        }

        OjJudgeInterface::save(array(
            'problem_id'    => $problemInfo['problem_id'],
            'language'      => $language,
            'source'        => $code,
            'user_id'       => $this->loginUserInfo['id'],
        ));

        // judge
        $this->renderAjax(0);
    }
}