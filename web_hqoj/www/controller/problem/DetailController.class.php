<?php

class DetailController extends ProjectController {

    public function defaultAction() {

        // 获取参数
        $id  = Request::getGET('global-id');

        // 校验题目
        $problemInfo = OjProblemInterface::getById(array('id' => $id));
        if (empty($problemInfo) || $problemInfo['hidden']) {
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