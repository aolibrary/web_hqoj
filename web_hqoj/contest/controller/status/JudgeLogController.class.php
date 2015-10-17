<?php

class JudgeLogController extends ProjectController {

    public function iframeShowAction() {

        $solutionId = (int) Request::getGET('solution-id');
        $solutionInfo = OjSolutionInterface::getDetail(array('id' => $solutionId));
        if (empty($solutionInfo) || false == OjSolutionHelper::hasLog($solutionInfo)) {
            $this->renderError();
        }

        // 获取属主
        $userId = $solutionInfo['user_id'];
        $userInfo = UserCommonInterface::getById(array('id' => $userId));

        // 是否有权限查看，竞赛管理员，自己的solution
        if (!$this->isContestAdmin && $solutionInfo['user_id'] != $this->loginUserInfo['id']) {
            $this->renderError('您没有权限查看！');
        }

        $this->renderIframe(array(
            'solutionInfo'  => $solutionInfo,
        ), 'status/iframe/judge_log.php');
    }
}
