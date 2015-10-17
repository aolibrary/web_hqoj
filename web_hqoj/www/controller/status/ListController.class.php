<?php

class ListController extends ProjectController {

    public function defaultAction() {

        $pageSize = 15;

        $username    = Request::getGET('username');
        $remote      = (int) Request::getGET('remote', -1);
        $problemCode = Request::getGET('problem-code');         // 因为ZOJ的题号是code
        $language    = (int) Request::getGET('language', -1);
        $result      = (int) Request::getGET('result', -1);
        $contestId   = (int) Request::getGET('contest-id');
        $globalId    = (int) Request::getGET('global-id');

        // 获取id
        $maxId = (int) Request::getGET('max-id', -1);
        $minId = (int) Request::getGET('min-id', -1);

        // 获取userInfo，username转换为userId
        $userInfo = array();
        if (!empty($username)) {
            $userInfo = UserCommonInterface::getByLoginName(array('login_name' => $username));
        }

        // 构建where
        $where = array();
        if ($this->isOjAdmin) {
            if ($contestId) {
                $where[] = array('contest_id', '=', $contestId);
            }
        }
        if (!empty($username)) {
            $where[] = array('user_id', '=', Arr::get('id', $userInfo, 0));
        }
        if ($remote != -1) {
            $where[] = array('remote', '=', $remote);
        }
        if (!empty($problemCode)) {
            $where[] = array('problem_code', '=', $problemCode);
        }
        if (!empty($globalId)) {
            $where[] = array('problem_global_id', '=', $globalId);
        }
        if ($language != -1) {
            $where[] = array('language', '=', $language);
        }
        if ($result != -1) {
            $where[] = array('result', '=', $result);
        }

        // 获取solutionList
        if ($maxId != -1) {
            $where[] = array('id', '<=', $maxId);
            $order = array(
                'id'    => 'DESC',
            );
            $solutionList = OjSolutionInterface::getList(array(
                'where' => $where,
                'order' => $order,
                'limit' => $pageSize,
                'include_contest' => $this->isOjAdmin,
            ));
        } else if ($minId != -1) {
            $where[] = array('id', '>=', $minId);
            $order = array(
                'id'    => 'ASC',
            );
            $solutionList = OjSolutionInterface::getList(array(
                'where' => $where,
                'order' => $order,
                'limit' => $pageSize,
                'include_contest' => $this->isOjAdmin,
            ));
            $solutionList = array_reverse($solutionList, true);
        } else {
            $order = array(
                'id'    => 'DESC',
            );
            $solutionList = OjSolutionInterface::getList(array(
                'where' => $where,
                'order' => $order,
                'limit' => $pageSize,
                'include_contest' => $this->isOjAdmin,
            ));
        }

        // 获取userHash
        $userIds = array_unique(array_column($solutionList, 'user_id'));
        $userHash = UserCommonInterface::getById(array('id' => $userIds));

        // 格式化solution
        foreach ($solutionList as &$solutionInfo) {

            // level
            $userInfo = $userHash[$solutionInfo['user_id']];
            list($solutionInfo['level'], $solutionInfo['permission']) = OjSolutionHelper::solutionPermission(
                $solutionInfo,
                $userInfo['share'],
                Arr::get('id', $this->loginUserInfo, 0),
                $this->isOjAdmin
            );
            $solutionInfo['has_log'] = OjSolutionHelper::hasLog($solutionInfo);
        }

        // 计算minId, maxId
        $minId = $maxId = 0;
        if (!empty($solutionList)) {
            $n = count($solutionList);
            $maxId = $solutionList[0]['id'];
            $minId = $solutionList[$n-1]['id'];
        }

        // 缓存html
        $html = array();
        $html['pager'] = $this->view->fetch(array(
            'renderMaxId' => $maxId,
            'renderMinId' => $minId,
        ), 'widget/pager_status.php');

        $tpl = $this->isOjAdmin ? 'status/list_admin.php' : 'status/list.php';

        // render
        $this->renderFramework(array(
            'userHash'     => $userHash,
            'solutionList' => $solutionList,
            'html'         => $html,
        ), $tpl);
    }

    public function ajaxRejudgeAction() {

        // 获取参数
        $solutionId = (int) Request::getPOST('solution-id');

        $solutionInfo = OjSolutionInterface::getById(array('id' => $solutionId));
        if (empty($solutionInfo)) {
            $this->renderError('Solution不存在！');
        }

        // 主站只有OJ管理员才可以重判，或者timeout
        if (!$this->isOjAdmin && $solutionInfo['result'] != StatusVars::TIME_OUT) {
            $this->renderError('你没有权限重判！');
        }

        // 重判
        OjSolutionInterface::rejudge(array('id' => $solutionId));
        $this->renderAjax(0);
    }

    public function ajaxHelpAction() {

        // 获取参数
        $solutionId = Request::getPOST('solution-id');

        $solutionInfo = OjSolutionInterface::getById(array('id' => $solutionId));
        if (empty($solutionInfo)) {
            $this->renderError('solution不存在！');
        }

        // 权限
        if (!$this->isOjAdmin && $solutionInfo['user_id'] != Arr::get('id', $this->loginUserInfo, 0)) {
            $this->renderError('你没有权限操作！');
        }

        $data = array(
            'id'    => $solutionId,
            'share' => 1-$solutionInfo['share']
        );

        OjSolutionInterface::save($data);
        $this->renderAjax(0);
    }
}
