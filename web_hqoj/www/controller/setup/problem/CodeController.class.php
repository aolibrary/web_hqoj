<?php

class CodeController extends ProjectBackendController {

    public function defaultAction() {

        $judgeId = (int) Request::getGET('judge-id');
        if (empty($judgeId)) {
            $this->renderError();
        }

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

        // 格式化
        $text  = StatusVars::$RESULT_FORMAT[$judgeInfo['result']];
        $class = StatusVars::$RESULT_CLASS[$judgeInfo['result']];
        $judgeInfo['result_html'] = sprintf('<span class="%s">%s</span>', $class, $text);
        $judgeInfo['source_format'] = htmlspecialchars($judgeInfo['source'], ENT_COMPAT, 'UTF-8');

        $this->renderFramework(array(
            'judgeInfo'  => $judgeInfo,
        ), 'setup/problem/code.php');
    }
}
