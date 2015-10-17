<?php

class ListController extends ProjectBackendController {

    public function defaultAction() {

        $pageSize = 20;

        // 获取参数
        $page   = Pager::get();
        $status = (int) Request::getGET('status');

        // 构建where
        $where = array();
        $where[] = array('remote', '=', StatusVars::REMOTE_HQU);
        $where[] = array('user_id', '=', $this->loginUserInfo['id']);
        if (!empty($status) && $status != -1) {
            $where[] = array('hidden', '=', $status - 1);
        }

        // 获取数据
        $offset = ($page-1)*$pageSize;
        $problemList = OjProblemInterface::getList(array(
            'where'     => $where,
            'limit'     => $pageSize,
            'offset'    => $offset,
        ));
        $allCount = OjProblemInterface::getCount($where);

        $userIds = array_unique(array_column($problemList, 'user_id'));
        $userHash = UserCommonInterface::getById(array('id' => $userIds));

        // 缓存部分的html
        $html = array();
        $html['pager'] = $this->view->fetch(array(
            'renderAllCount' => $allCount,
            'renderPageSize' => $pageSize,
            'renderRadius'   => 5,
        ), 'widget/pager.php');

        // 输出
        $this->renderFramework(array(
            'problemList'   => $problemList,
            'userHash'      => $userHash,
            'html'          => $html,
        ), 'setup/problem/list.php');
    }

}
