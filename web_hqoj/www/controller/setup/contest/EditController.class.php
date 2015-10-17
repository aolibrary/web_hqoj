<?php

class EditController extends ProjectBackendController {

    public function defaultAction() {

        $contestId = (int) Request::getGET('contest-id');
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
        ), 'setup/contest/edit.php');
    }

    public function ajaxSubmitAction() {

        // 获取参数
        $contestId      = (int) Request::getPOST('contest-id');
        $title          = trim(Request::getPOST('title'));
        $type           = Request::getPOST('type');
        $password       = trim(Request::getPOST('password'));
        $notice         = trim(Request::getPOST('notice'));
        $beginTime      = strtotime(trim(Request::getPOST('begin-time')));
        $endTime        = strtotime(trim(Request::getPOST('end-time')));
        $description    = Request::getPOST('description', '', true);
        $problemHidden  = Request::getPOST('problem-hidden', 0);

        // 参数校验1
        if (empty($title) || mb_strlen($title, 'utf8') > 50) {
            $this->renderError('标题必填，限制50个字以内！');
        }
        if (mb_strlen($notice, 'utf8') > 100) {
            $this->renderError('提示限制100个字以内！');
        }
        if (!preg_match('/^[A-Za-z0-9_]{0,20}$/', $password)) {
            $this->renderError('密码格式不合法！');
        }
        if ($type == ContestVars::TYPE_PASSWORD && empty($password)) {
            $this->renderError('密码不能为空！');
        }
        if (empty($type) || !array_key_exists($type, ContestVars::$TYPE_FORMAT)) {
            $this->renderError('请选择比赛访问权限！');
        }

        // 竞赛是否存在
        $contestInfo = OjContestInterface::getById(array('id' => $contestId));
        if (empty($contestInfo)) {
            $this->renderError('竞赛不存在！');
        }

        // 权限
        if ($contestInfo['user_id'] != $this->loginUserInfo['id']) {
            $this->renderError('你没有权限操作！');
        }

        // 参数校验2
        if ($contestInfo['is_active']) {
            $beginTime = $contestInfo['begin_time'];
        }
        if (empty($beginTime) || empty($endTime) || $beginTime >= $endTime) {
            $this->renderError('比赛时间不合法！');
        }

        // 时间不能超过1年
        if ($endTime - $beginTime > 366*86400) {
            $this->renderError('比赛时间不能超过1年！');
        }

        $data = array(
            'id'             => $contestId,
            'title'          => $title,
            'type'           => $type,
            'password'       => $password,
            'notice'         => $notice,
            'begin_time'     => $beginTime,
            'end_time'       => $endTime,
            'description'    => $description,
            'problem_hidden' => $problemHidden ? 1 : 0,
        );
        OjContestInterface::save($data);
        $this->setNotice(FrameworkVars::NOTICE_SUCCESS, '操作成功！');
        $this->renderAjax(0);
    }

}
