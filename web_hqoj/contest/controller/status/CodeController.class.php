<?php

class CodeController extends ProjectController {

    public function defaultAction() {

        $solutionId = (int) Request::getGET('solution-id');

        // 获取solutionInfo
        $solutionInfo = OjSolutionInterface::getDetail(array('id' => $solutionId));
        if (empty($solutionInfo)) {
            $this->renderError();
        }

        // 获取属主
        $userId = $solutionInfo['user_id'];
        $userInfo = UserCommonInterface::getById(array('id' => $userId));
        if (empty($userInfo)) {
            $this->renderError();
        }

        // 是否有权限查看，竞赛管理员，自己的solution
        if (!$this->isContestAdmin && $solutionInfo['user_id'] != $this->loginUserInfo['id']) {
            $this->renderError('您没有权限查看！');
        }

        $this->renderFramework(array(
            'solutionInfo'  => $solutionInfo,
        ), 'status/code.php');
    }

}
