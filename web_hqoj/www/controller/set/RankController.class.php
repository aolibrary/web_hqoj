<?php

class RankController extends ProjectController {

    public function defaultAction() {

        $setId = (int) Request::getGET('set-id');
        if (empty($setId)) {
            $this->renderError();
        }

        $setInfo = OjProblemSetInterface::getById(array('id' => $setId));
        if (empty($setInfo)) {
            $this->renderError();
        }
        if (!$this->isOjAdmin && $setInfo['hidden'] && Arr::get('id', $this->loginUserInfo['id'], 0) != $setInfo['user_id']) {
            $this->renderError();
        }

        $setInfo['global_ids'] = (array) json_decode($setInfo['problem_set'], true);

        list($rankHash, $mat, $userHash) = OjProblemSetInterface::getRankBoard(array('id' => $setId));

        $this->renderFramework(array(
            'setInfo'   => $setInfo,
            'rankHash'  => $rankHash,
            'mat'       => $mat,
            'userHash'  => $userHash,
        ), 'set/rank.php');
    }
}
