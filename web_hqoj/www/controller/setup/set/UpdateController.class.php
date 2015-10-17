<?php

class UpdateController extends ProjectBackendController {

    public function iframeUpdateAction() {

        $setId = Request::getGET('set-id');

        if (empty($setId)) {
            $this->renderError('参数错误');
        }

        $setInfo = OjProblemSetInterface::getById(array('id' => $setId));
        if (empty($setInfo)) {
            $this->renderError();
        }

        // 输出
        $this->renderIframe(array(
            'setInfo'   => $setInfo,
        ), 'setup/set/iframe/update.php');
    }

    public function ajaxSubmitAction() {

        $setId = Request::getPOST('set-id');
        $title = trim(Request::getPOST('title'));
        $refreshAt = strtotime(trim(Request::getPOST('refresh-at')));

        if (empty($setId)) {
            $this->renderError('参数错误！');
        }
        if (empty($title) || mb_strlen($title, 'utf8') > 50) {
            $this->renderError('标题必填，限制50个字以内！');
        }

        $currentDay = intval(time()/86400)*86400+86400;
        if (empty($refreshAt) || $refreshAt > $currentDay) {
            $this->renderError('刷新时间不能超过今天！');
        }

        $setInfo = OjProblemSetInterface::getById(array('id' => $setId));
        if (empty($setInfo)) {
            $this->renderError('专题不存在！');
        }

        // 属主验证
        if ($setInfo['user_id'] != $this->loginUserInfo['id']) {
            $this->renderError('你没有修改权限！');
        }

        $data = array(
            'id'            => $setId,
            'title'         => $title,
            'refresh_at'    => $refreshAt,
        );
        OjProblemSetInterface::save($data);

        $this->setNotice(FrameworkVars::NOTICE_SUCCESS, '修改成功！');
        $this->renderAjax(0);
    }
}
