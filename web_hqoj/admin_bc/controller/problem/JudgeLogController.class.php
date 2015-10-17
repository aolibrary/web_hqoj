<?php

class JudgeLogController extends ProjectController {

    public function iframeShowAction() {

        $judgeId = (int) Request::getGET('judge-id');

        // 获取judgeInfo
        $judgeInfo = OjJudgeInterface::getRow(array('id' => $judgeId));
        if (empty($judgeInfo)) {
            $this->renderError();
        }

        $this->renderIframe(array(
            'judgeInfo'  => $judgeInfo,
        ), 'problem/iframe/judge_log.php');
    }
}
