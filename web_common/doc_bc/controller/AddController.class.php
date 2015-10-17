<?php

class AddController extends ProjectController {

    public function ajaxAddAction() {

        $data = array(
            'user_id' => $this->loginUserInfo['id'],
        );
        DocInterface::save($data);
        $this->setNotice(FrameworkVars::NOTICE_SUCCESS, '添加了新文章');
        $this->renderAjax(0);
    }
}
