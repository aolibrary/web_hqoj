<?php

class ListController extends ProjectController {

    public function defaultAction() {

        $pageSize = 20;

        $page   = Pager::get();
        $level  = (int) Request::getGET('level');
        $key    = Request::getGET('key');

        $where = array();
        if ($key == 'phpErrors') {
            $where[] = array('tag', 'IN', LoggerKeys::$phpErrors);
        } else if (!empty($key)) {
            $where[] = array('tag', '=', $key);
        }
        if (!empty($level)) {
            $where[] = array( 'level', '=', $level );
        }

        $logList    = LoggerInterface::getList(array(
            'field'     => '*',
            'where'     => $where,
            'limit'     => $pageSize,
            'offset'    => ($page-1)*$pageSize,
        ));
        $allCount   = LoggerInterface::getCount($where);

        // 缓存部分的html
        $html = array();
        $html['pager'] = $this->view->fetch(array(
            'renderAllCount' => $allCount,
            'renderPageSize' => $pageSize,
            'renderRadius'   => 4,
        ), 'widget/pager_long.php');

        $this->renderFramework(array(
            'html'      => $html,
            'logList'   => $logList,
        ), 'list.php');
    }

}