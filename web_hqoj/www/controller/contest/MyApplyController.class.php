<?php

class MyApplyController extends ProjectController {

    public function iframeApplyAction() {

        if (empty($this->loginUserInfo)) {
            $this->renderError('请登录！');
        }

        $contestId = (int) Request::getGET('contest-id');
        $contestInfo = OjContestInterface::getById(array('id' => $contestId));
        if (empty($contestInfo)) {
            $this->renderError('竞赛不存在！');
        }
        if ($contestInfo['type'] != ContestVars::TYPE_APPLY) {
            $this->renderError('该竞赛不需要报名！');
        }

        // 当前报名信息
        $applyInfo = OjContestApplyInterface::getDetail(array(
            'contest_id'    => $contestId,
            'user_id'       => $this->loginUserInfo['id'],
        ));

        // 最近一次报名信息
        $preApplyInfo = $applyInfo;
        if (empty($applyInfo)) {
            $preApplyInfo = OjContestApplyInterface::getLastInfo(array('user_id' => $this->loginUserInfo['id']));
        }

        $this->renderIframe(array(
            'contestInfo'   => $contestInfo,
            'preApplyInfo'  => $preApplyInfo,
            'applyInfo'     => $applyInfo,
        ), 'contest/iframe/my_apply.php');

    }

    public function ajaxApplyAction() {

        if (empty($this->loginUserInfo)) {
            $this->renderError('请登录！');
        }

        $contestId  = (int) Request::getPOST('contest-id');
        $realName   = trim(Request::getPOST('real-name'));
        $xuehao     = trim(Request::getPOST('xuehao'));
        $xueyuan    = (int) Request::getPOST('xueyuan');
        $sex        = (int) Request::getPOST('sex');

        // 校验
        if (empty($realName) || empty($xuehao) || empty($xueyuan) || empty($sex)
            || !array_key_exists($xueyuan, ContestVars::$XUEYUAN) || !in_array($sex, array(1, 2))
            || mb_strlen($realName, 'utf8') < 2 || mb_strlen($realName, 'utf8') > 4) {
            $this->renderError('参数错误！');
        }

        $contestInfo = OjContestInterface::getById(array('id' => $contestId));
        if (empty($contestInfo)) {
            $this->renderError('比赛不存在！');
        }
        if ($contestInfo['type'] != ContestVars::TYPE_APPLY) {
            $this->renderError('比赛不需要报名！');
        }
        if ($contestInfo['end_time'] < time()) {
            $this->renderError('比赛已经结束！');
        }

        $applyInfo = OjContestApplyInterface::getDetail(array(
            'contest_id'    => $contestId,
            'user_id'       => $this->loginUserInfo['id'],
        ));
        if (!empty($applyInfo) && $applyInfo['status'] == ContestVars::APPLY_ACCEPTED) {
            $this->renderError('报名已通过，无法修改！');
        }

        $data = array(
            'contest_id' => $contestId,
            'user_id'    => $this->loginUserInfo['id'],
            'real_name'  => $realName,
            'xuehao'     => $xuehao,
            'xueyuan'    => $xueyuan,
            'sex'        => $sex,
        );
        OjContestApplyInterface::save($data);
        $this->renderAjax(0);
    }
}
