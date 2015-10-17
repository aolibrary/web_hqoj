<?php

class CodeController extends ProjectController {

    public function defaultAction() {

        $judgeId = (int) Request::getGET('judge-id');
        if (empty($judgeId)) {
            $this->renderError();
        }

        $judgeInfo = OjJudgeInterface::getById(array('id' => $judgeId));
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
        ), 'problem/code.php');
    }
}
