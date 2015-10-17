<?php

class ListController extends ProjectController {

    public function defaultAction() {

        $pageSize = 20;

        // 获取参数
        $page   = Pager::get();
        $title  = Request::getGET('title');
        $passed = (int) Request::getGET('passed', 0);
        $diy    = (int) Request::getGET('diy', 0);

        // 构建where
        $where = array();
        $where[] = array('hidden', '=', 0);
        if ($passed) {
            $where[] = array('end_time', '<=', time());
        } else {
            $where[] = array('end_time', '>', time());
        }
        if ($diy) {
            $where[] = array('is_diy', '=', 1);
        } else {
            $where[] = array('is_diy', '=', 0);
        }
        if (!empty($title)) {
            $where[] = array('title', 'LIKE', "%{$title}%");
        }

        if ($passed) {
            $order = array('end_time' => 'DESC');
        } else {
            $order = array('end_time' => 'ASC');
        }

        // 获取数据
        $offset = ($page-1)*$pageSize;
        $tmpContestList = OjContestInterface::getList(array(
            'where'     => $where,
            'order'     => $order,
            'limit'     => $pageSize,
            'offset'    => $offset,
        ));
        $allCount = OjContestInterface::getCount($where);

        // 将进行中的比赛提前
        $contestList = array();
        if (! $passed) {
            foreach ($tmpContestList as $i => $contestInfo) {
                if ($contestInfo['begin_time'] < time()) {
                    $contestList[] = $contestInfo;
                    unset($tmpContestList[$i]);
                }
            }
            foreach ($tmpContestList as $i => $contestInfo) {
                $contestList[] = $contestInfo;
                unset($tmpContestList[$i]);
            }
        } else {
            $contestList = $tmpContestList;
        }

        $userIds = array_unique(array_column($contestList, 'user_id'));
        $userHash = UserCommonInterface::getById(array('id' => $userIds));

        // 格式化
        foreach ($contestList as &$contestInfo) {
            // row_class
            $now = time();
            if ($contestInfo['end_time'] < $now) {
                $contestInfo['row_css'] = 'passed';
            } else if ($contestInfo['begin_time'] > $now) {
                $contestInfo['row_css'] = 'pending';
            } else {
                $contestInfo['row_css'] = 'running';
            }
            // type_format
            $type = $contestInfo['type'];
            if ($type == ContestVars::TYPE_PUBLIC) {
                $contestInfo['type_format'] = '<p class="red">公开</p>';
            } else if ($type == ContestVars::TYPE_APPLY) {
                $contestInfo['type_format'] = '<p class="orange">报名</p>';
            } else if ($type == ContestVars::TYPE_PASSWORD) {
                $contestInfo['type_format'] = '<p class="green">密码</p>';
            } else {
                $contestInfo['type_format'] = '<p class="gray">未定义</p>';
            }
        }

        // 缓存部分的html
        $html = array();
        $html['pager'] = $this->view->fetch(array(
            'renderAllCount' => $allCount,
            'renderPageSize' => $pageSize,
            'renderRadius'   => 8,
        ), 'widget/pager.php');

        // 输出
        $this->renderFramework(array(
            'contestList'   => $contestList,
            'userHash'      => $userHash,
            'html'          => $html,
        ), 'contest/list.php');
    }
}
