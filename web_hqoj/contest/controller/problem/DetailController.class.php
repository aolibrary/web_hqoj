<?php

class DetailController extends ProjectController {

    public function defaultAction() {

        // 获取参数
        $problemHash = Request::getGET('problem-hash');

        $globalId = array_search($problemHash, $this->contestInfo['problem_hash']);
        if (empty($globalId)) {
            $this->renderError('竞赛中无此题！');
        }

        // 获取题目
        $problemInfo = OjProblemInterface::getById(array('id' => $globalId));
        if (empty($problemInfo)) {
            $this->renderError('题目不存在！');
        }

        $remote      = $problemInfo['remote'];
        $problemId   = $problemInfo['problem_id'];
        $problemCode = $problemInfo['problem_code'];

        if ($remote) {
            $srcUrl = OjCommonHelper::getSrcUrl($remote, $problemId, $problemCode);
            $this->renderFramework(array(
                'srcUrl'        => $srcUrl,
                'problemInfo'   => $problemInfo,
            ), 'problem/detail_remote.php');
        } else {
            $this->renderFramework(array(
                'problemInfo' => $problemInfo,
            ), 'problem/detail.php');
        }
    }
}