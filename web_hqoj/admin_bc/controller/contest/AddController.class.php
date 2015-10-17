<?php

class AddController extends ProjectController {

    public function ajaxAddAction() {

        // 插入空数据
        $data = array(
            'is_diy'  => 0,
            'user_id' => $this->loginUserInfo['id'],
        );
        OjContestInterface::save($data);

        $this->setNotice(FrameworkVars::NOTICE_SUCCESS, '您成功创建了比赛，请编辑！');
        $this->renderAjax(0);
    }
}
