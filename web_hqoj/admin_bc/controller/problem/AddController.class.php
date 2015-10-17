<?php

class AddController extends ProjectController {

    public function ajaxAddAction() {

        $data = array(
            'remote'        => StatusVars::REMOTE_HQU,
            'user_id'       => $this->loginUserInfo['id'],
        );
        OjProblemInterface::save($data);

        $msg = '你成功创建了题目，请记得编辑题目哦！';
        $this->setNotice(FrameworkVars::NOTICE_SUCCESS, $msg);
        $this->renderAjax(0);
    }
}
