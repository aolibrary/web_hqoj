<?php

class DetailController extends ProjectController {

    public function defaultAction() {

        $contestId = Request::getGET('contest-id');
        $contestInfo = OjContestInterface::getById(array('id' => $contestId));
        if (empty($contestInfo)) {
            $this->renderError('竞赛不存在！');
        }

        $this->renderFramework(array(
            'contestInfo'   => $contestInfo,
        ), 'contest/detail.php');
    }

}
