<?php

class InvalidController extends ProjectController {

    public function defaultAction() {

        $pathList = RootRelationInterface::getInvalidPathList();

        $this->renderFramework(array(
            'pathList'  => $pathList,
        ), 'manager/invalid.php');
    }

    public function ajaxDeleteAction() {

        $path = Request::getPOST('path', '');
        if (empty($path)) {
            $this->renderAjax(1, '参数错误！');
        }

        RootRelationInterface::deleteByPath(array('path' => $path));
        $this->setNotice(FrameworkVars::NOTICE_SUCCESS, '操作成功！');
        $this->renderAjax(0);
    }
}