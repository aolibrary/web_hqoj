<?php

class ListController extends ProjectController {

    public function defaultAction() {

        list($rankHash, $mat, $userHash) = OjContestInterface::getRankBoard(array('id' => $this->contestInfo['id']));

        // 如果是报名，获取报名列表
        $applyHash = array();
        if ($this->contestInfo['type'] == ContestVars::TYPE_APPLY) {
            $where = array(
                array('contest_id', '=', $this->contestInfo['id']),
            );
            $applyHash = OjContestApplyInterface::getList(array(
                'where' => $where,
            ));
            $applyHash = Arr::listToHash('user_id', $applyHash);
        }

        $this->renderFramework(array(
            'rankHash'  => $rankHash,
            'mat'       => $mat,
            'userHash'  => $userHash,
            'applyHash' => $applyHash,
        ), 'rank/list.php');
    }
}