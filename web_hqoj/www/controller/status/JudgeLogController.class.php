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

        // solution level
        list($solutionInfo['level'], $solutionInfo['permission']) = OjSolutionHelper::solutionPermission(
            $solutionInfo,
            $userInfo['share'],
            Arr::get('id', $this->loginUserInfo, 0),
            $this->isOjAdmin
        );
        if (!$solutionInfo['permission']) {
            $this->renderError('您没有权限查看！');
        }

        $this->renderIframe(array(
            'solutionInfo'  => $solutionInfo,
        ), 'status/iframe/judge_log.php');
    }
}
