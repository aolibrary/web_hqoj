<?php

class AddController extends ProjectBackendController {

    public function ajaxSubmitAction() {

        // 如果空专题超过20，提示编辑
        $where = array(
            array('user_id', '=', $this->loginUserInfo['id']),
            array('title', '=', ''),
        );
        $count = OjProblemSetInterface::getCount($where);
        if ($count > 20) {
            $this->renderError('您有很多空的专题，请先编辑！');
        }

        // 创建空的专题
        $data = array(
            'user_id' => $this->loginUserInfo['id'],
        );
        OjProblemSetInterface::save($data);

        $this->setNotice(FrameworkVars::NOTICE_SUCCESS, '您成功创建了专题，请编辑！');
        $this->renderAjax(0);
    }
}
