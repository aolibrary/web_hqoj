<?php

class ListController extends ProjectController {

    public function defaultAction() {

        $pageSize = 20;

        // 获取参数
        $page       = Pager::get();
        $keyword    = Request::getGET('keyword');

        // 构建where
        $where = array();
        if (!empty($keyword)) {
            $where[] = array('OR' => array(
                array('code', 'LIKE', "%{$keyword}%"),
                array('description', 'LIKE', "%{$keyword}%"),
            ));
        }

        // 查询
        $offset = ($page-1)*$pageSize;
        $permissionList = RootPermissionInterface::getList(array(
            'where'     => $where,
            'limit'     => $pageSize,
            'offset'    => $offset,
        ));
        $allCount = RootPermissionInterface::getCount($where);

        // 缓存部分的html
        $html = array();
        $html['pager'] = $this->view->fetch(array(
            'renderAllCount' => $allCount,
            'renderPageSize' => $pageSize,
            'renderRadius'   => 8,
        ), 'widget/pager.php');

        $this->renderFramework(array(
            'html'              => $html,
            'permissionList'    => $permissionList,
        ), 'permission/list.php');
    }
}
