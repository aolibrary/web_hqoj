<?php

class AddController extends ProjectBackendController {

    public function ajaxAddAction() {

        // 判断用户可以创建的题目是否上限
        $where = array(
            array('remote', '=', StatusVars::REMOTE_HQU),
            array('user_id', '=', $this->loginUserInfo['id']),
            array('hidden', '=', 1),
        );

        $count = OjProblemInterface::getCount($where);
        if ($count >= 20) {
            $this->renderError('你的私有题目达到上限（20题）！');
        }

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
