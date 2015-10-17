<?php

class ListController extends ProjectController {

    public function defaultAction() {

        // 如果已经选定专题，那么跳转
        if (Request::getGET('from') == 'nav') {
            $setId = (int) Cookie::get('current_set');
            if ($setId) {
                $url = '/set_problem/?set-id=' . $setId;
                Url::redirect($url);
            }
        }

        Cookie::delete('current_set');

        $pageSize = 50;

        // 获取参数
        $page     = Pager::get();
        $title    = Request::getGET('title');
        $username = Request::getGET('username');

        // 构建where
        $where = array();
        $where[] = array('hidden', '=', 0);
        if (!empty($title)) {
            $where[] = array('title', 'LIKE', "%{$title}%");
        }
        if (!empty($username)) {
            $userInfo = UserCommonInterface::getByLoginName(array('login_name' => $username));
            $where[] = array('user_id', '=', Arr::get('id', $userInfo, 0));
        }

        // 获取列表
        $order = array(
            'listing_status'    => 'DESC',
            'refresh_at'        => 'DESC',
            'id'                => 'DESC',
        );
        $offset = ($page-1)*$pageSize;
        $setList = OjProblemSetInterface::getList(array(
            'where'     => $where,
            'order'     => $order,
            'limit'     => $pageSize,
            'offset'    => $offset,
        ));
        $allCount = OjProblemSetInterface::getCount($where);

        foreach ($setList as &$setInfo) {
            $problemJson = $setInfo['problem_set'];
            $globalIds = json_decode($problemJson, true);
            $setInfo['count'] = count($globalIds);
        }

        // 获取用户
        $userHash = array();
        if (!empty($setList)) {
            $userIds = array_unique(array_column($setList, 'user_id'));
            $userHash = UserCommonInterface::getById(array('id' => $userIds));
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
            'html'      => $html,
            'setList'   => $setList,
            'userHash'  => $userHash,
        ), 'set/list.php');
    }
}
