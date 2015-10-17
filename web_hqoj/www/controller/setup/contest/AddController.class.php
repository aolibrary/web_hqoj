<?php

class AddController extends ProjectBackendController {

    public function ajaxAddAction() {

        // 如果非激活的比赛超过50，那么提示编辑非激活的比赛
        $where = array(
            array('is_active', '=', 0),
            array('is_diy', '=', 1),
            array('user_id', '=', $this->loginUserInfo['id']),
        );
        $count = OjContestInterface::getCount($where);
        if ($count > 50) {
            $this->renderError('您有太多比赛没有编辑哦，请直接编辑！');
        }

        // 插入空数据
        $data = array(
            'is_diy'  => 1,
            'user_id' => $this->loginUserInfo['id'],
        );
        OjContestInterface::save($data);
        $this->setNotice(FrameworkVars::NOTICE_SUCCESS, '您成功创建了DIY比赛，请编辑！');
        $this->renderAjax(0);
    }
}
