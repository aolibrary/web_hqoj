<?php

class DetailController extends ProjectBackendController {

    public function defaultAction() {

        $contestId = Request::getGET('contest-id');
        $contestInfo = OjContestInterface::getById(array('id' => $contestId));
        if (empty($contestInfo)) {
            $this->renderError('竞赛不存在！');
        }

        // 权限
        if ($contestInfo['user_id'] != $this->loginUserInfo['id']) {
            $this->renderError('你没有权限查看！');
        }

        $this->renderFramework(array(
            'contestInfo'   => $contestInfo,
        ), 'setup/contest/detail.php');
    }

}
