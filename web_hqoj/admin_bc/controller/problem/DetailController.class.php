<?php

class DetailController extends ProjectController {

    public function defaultAction() {

        // 获取参数
        $globalId  = (int) Request::getGET('global-id');
        $problemId = Request::getGET('problem-id');

        $problemInfo = array();
        if (!empty($globalId)) {
            $problemInfo = OjProblemInterface::getById(array('id' => $globalId));
        } else if (!empty($problemId)) {
            $problemInfo = OjProblemInterface::getDetail(array(
                'remote'        => StatusVars::REMOTE_HQU,
                'problem_id'    => $problemId,
            ));
        }
        if (empty($problemInfo)) {
            $this->renderError('题目不存在！');
        }

        $this->renderFramework(array(
            'problemInfo' => $problemInfo,
        ), 'problem/detail.php');
    }
}