<?php

class JudgeLogController extends ProjectBackendController {

    public function iframeShowAction() {

        $judgeId = (int) Request::getGET('judge-id');

        // 获取judgeInfo
        $where = array(
            array('id', '=', $judgeId),
            array('solution_id', '=', 0),
            array('user_id', '=', $this->loginUserInfo['id']),
        );
        $judgeInfo = OjJudgeInterface::getRow(array(
            'where' => $where,
        ));
        if (empty($judgeInfo)) {
            $this->renderError();
        }

        $this->renderIframe(array(
            'judgeInfo'  => $judgeInfo,
        ), 'setup/problem/iframe/judge_log.php');
    }
}
