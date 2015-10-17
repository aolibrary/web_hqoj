<?php

class EditController extends ProjectBackendController {

    public function defaultAction() {

        $globalId = Request::getGET('global-id');

        $problemInfo = OjProblemInterface::getById(array('id' => $globalId));
        if (empty($problemInfo) || !$problemInfo['hidden']
            || $problemInfo['user_id'] != $this->loginUserInfo['id']) {
            $this->renderError('你没有操作权限！');
        }

        $this->renderFramework(array(
            'problemInfo'   => $problemInfo,
        ), 'setup/problem/edit.php');
    }

    public function ajaxSubmitAction() {

        // 获取参数
        $globalId     = (int) Request::getPOST('global-id');
        $title        = trim(Request::getPOST('title'));
        $source       = trim(Request::getPOST('source'));
        $timeLimit    = max(1, Request::getPOST('time-limit'));
        $memoryLimit  = max(32, Request::getPOST('memory-limit'));
        $description  = Request::getPOST('description', '', true);
        $input        = Request::getPOST('input', '', true);
        $output       = Request::getPOST('output', '', true);
        $sampleInput  = Request::getPOST('sample-input', '', true);
        $sampleOutput = Request::getPOST('sample-output', '', true);
        $hint         = Request::getPOST('hint', '', true);

        if (!in_array($timeLimit, StatusVars::$TIME_LIMIT) || !in_array($memoryLimit, StatusVars::$MEMORY_LIMIT)) {
            $this->renderError('参数错误！');
        }

        // 校验
        if (mb_strlen($title, 'utf8') > 50) {
            $this->renderError('标题限制50个字以内！');
        }
        if (mb_strlen($source, 'utf8') > 50) {
            $this->renderError('来源限制50个字以内！');
        }

        // 校验problem
        $problemInfo = OjProblemInterface::getById(array('id' => $globalId));
        if (empty($problemInfo) || !$problemInfo['hidden']
            || $problemInfo['user_id'] != $this->loginUserInfo['id']) {
            $this->renderError('你没有操作权限！');
        }

        // 更新
        $data = array(
            'id'            => $globalId,
            'title'         => $title,
            'source'        => str_replace('，', ',', $source),
            'time_limit'    => $timeLimit*1000,
            'memory_limit'  => $memoryLimit*1024,
            'description'   => $description,
            'input'         => $input,
            'output'        => $output,
            'sample_input'  => $sampleInput,
            'sample_output' => $sampleOutput,
            'hint'          => $hint,
        );
        OjProblemInterface::save($data);

        // 记录编辑历史
        if ($problemInfo['remote'] == StatusVars::REMOTE_HQU) {
            $dateTime = date('Y-m-d H:i:s', time());
            $history = "<p>{$dateTime} 用户{$this->loginUserInfo['username']}编辑了题目</p>";
            OjProblemInterface::auditHistory(array(
                'problem_id'     => $problemInfo['problem_id'],
                'append_history' => $history,
            ));
        }

        $this->setNotice(FrameworkVars::NOTICE_SUCCESS, '编辑成功！');
        $this->renderAjax(0);
    }

}
