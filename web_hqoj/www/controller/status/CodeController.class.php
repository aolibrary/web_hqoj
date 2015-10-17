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

        $this->renderFramework(array(
            'solutionInfo'  => $solutionInfo,
        ), 'status/code.php');
    }
}
