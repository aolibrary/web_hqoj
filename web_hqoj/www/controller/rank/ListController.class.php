<?php

class ListController extends ProjectController {

    public function defaultAction() {

        $pageSize = 25;

        $page = Pager::get();

        $order = array(
            'solved_all'    => 'DESC',
            'submit_all'    => 'DESC',
            'id'            => 'ASC',
        );
        $offset = ($page-1)*$pageSize;
        $userList = UserCommonInterface::getList(array(
            'order'     => $order,
            'limit'     => $pageSize,
            'offset'    => $offset,
        ));
        $allCount = UserCommonInterface::getCount();

        // 缓存部分的html
        $html = array();
        $html['pager'] = $this->view->fetch(array(
            'renderAllCount' => $allCount,
            'renderPageSize' => $pageSize,
            'renderRadius'   => 10,
        ), 'widget/pager.php');

        // 输出
        $this->renderFramework(array(
            'userList'  => $userList,
            'html'      => $html,
            'beginRank' => $offset+1,
        ), 'rank/list.php');
    }
}
