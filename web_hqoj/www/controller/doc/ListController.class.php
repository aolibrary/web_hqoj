<?php

class ListController extends ProjectController {

    public function defaultAction() {

        $docId = (int) Request::getGET('doc-id', 0);

        $docInfo = array();
        if (!empty($docId)) {
            $docInfo = DocInterface::getById(array('id' => $docId));
            if (empty($docInfo) || $docInfo['hidden'] || $docInfo['category'] != 1) {
                $this->renderError('文章不存在！');
            }
        }

        $where = array();
        $where[] = array('category', '=', 1);
        $where[] = array('hidden', '=', 0);
        $where[] = array('title', '!=', '');

        $order = array(
            'title' => 'ASC',
        );
        $docList = DocInterface::getList(array(
            'where' => $where,
            'order' => $order,
        ));

        $this->renderFramework(array(
            'docList' => $docList,
            'docInfo' => $docInfo,
        ), 'doc/list.php');
    }
}