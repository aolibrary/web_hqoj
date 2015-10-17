<?php

class DetailController extends ProjectController {

    public function defaultAction() {

        $docId = (int) Request::getGET('doc-id');
        $docInfo = DocInterface::getById(array('id' => $docId));
        if (empty($docInfo) || $docInfo['hidden']) {
            $this->renderError('文章不存在！');
        }

        // userInfo
        $userInfo = UserCommonInterface::getById(array('id' => $docInfo['user_id']));

        $this->renderFramework(array(
            'docInfo'   => $docInfo,
            'userInfo'  => $userInfo,
        ), 'doc/detail.php');

    }
}