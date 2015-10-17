<?php

class ListController extends ProjectController {

    public function defaultAction() {

        $pageSize = 100;

        // 获取参数
        $page       = Pager::get();
        $remote     = (int) Request::getGET('remote', 0);
        $keyword    = Request::getGET('keyword');
        $searchType = (int) Request::getGET('search-type', 1);

        // 构建where
        $where = array();
        $where[] = array('remote', '=', $remote);
        $where[] = array('hidden', '=', 0);
        if (!empty($keyword)) {
            if ($searchType == 1) {
                $where[] = array('OR' => array(
                    array('problem_code', '=', $keyword),
                    array('title', 'LIKE', "%{$keyword}%"),
                ));
            } else if ($searchType == 2) {
                $where[] = array('OR' => array(
                    array('problem_code', '=', $keyword),
                    array('source', 'LIKE', "%{$keyword}%"),
                ));
            }
        }

        // 获取数据
        $order = array(
            'problem_code'  => 'ASC',
        );
        $offset = ($page - 1)*$pageSize;
        $problemList = OjProblemInterface::getList(array(
            'where'     => $where,
            'order'     => $order,
            'limit'     => $pageSize,
            'offset'    => $offset,
        ));
        $allCount = OjProblemInterface::getCount($where);

        // 获取用户解决的题目
        $userSolution = array();
        if ($this->loginUserInfo) {
            $globalIds = array_column($problemList, 'id');
            $where = array(
                array('user_id', '=', $this->loginUserInfo['id']),
                array('contest_id', '=', 0),
                array('problem_global_id', 'IN', $globalIds),
            );
            $solutionList = OjSolutionInterface::getList(array(
                'where' => $where,
            ));
            foreach ($solutionList as $solutionId => $solutionInfo) {
                $globalId = $solutionInfo['problem_global_id'];
                if (!array_key_exists($globalId, $userSolution) || $solutionInfo['result'] == StatusVars::ACCEPTED) {
                    $userSolution[$globalId] = $solutionInfo;
                }
            }
        }

        $userHash = array();
        if ($allCount > 0) {
            $userIds = array_unique(array_column($problemList, 'user_id'));
            $userHash = UserCommonInterface::getById(array('id' => $userIds));
        }

        // 缓存部分的html
        $html = array();
        $html['pager'] = $this->view->fetch(array(
            'renderAllCount' => $allCount,
            'renderPageSize' => $pageSize,
            'renderRadius'   => 10,
        ), 'widget/pager.php');

        $tpl = $remote ? 'problem/list_remote.php' : 'problem/list.php';

        // 输出
        $this->renderFramework(array(
            'html'          => $html,
            'problemList'   => $problemList,
            'userSolution'  => $userSolution,
            'userHash'      => $userHash,
        ), $tpl);
    }
}
