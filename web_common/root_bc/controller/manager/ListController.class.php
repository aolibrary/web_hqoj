<?php

class ListController extends ProjectController {

    public function defaultAction() {

        $pageSize = 20;

        $page        = Pager::get();
        $loginName   = Request::getGET('login-name', '');
        $path        = Request::getGET('path', '');
        $includePath = Request::getGET('include-path', '');

        // 路径非法提示
        if (!empty($path)) {
            if (!RootPermissionInterface::isValidPath(array('path' => $path))) {
                $this->setNotice(FrameworkVars::NOTICE_ERROR, "路径{$path}格式不正确！");
                $url = Url::getCurrentUrl(array('path' => null));
                Url::redirect($url);
            }
        }

        // 路径非法提示
        if (!empty($includePath)) {
            if (!RootPermissionInterface::isValidPath(array('path' => $includePath))) {
                $this->setNotice(FrameworkVars::NOTICE_ERROR, "路径{$includePath}格式不正确！");
                $url = Url::getCurrentUrl(array('include-path' => null));
                Url::redirect($url);
            }
        }

        // 用户不存在提示
        if (!empty($loginName)) {
            $userInfo = UserCommonInterface::getByLoginName(array('login_name' => $loginName));
            if (empty($userInfo)) {
                $this->setNotice(FrameworkVars::NOTICE_ERROR, '用户不存在！');
                $url = Url::getCurrentUrl(array('login-name' => null));
                Url::redirect($url);
            }
        }

        // 构建where
        $where = array();
        if (!empty($userInfo)) {
            $where[] = array('user_id', '=', $userInfo['id']);
        }
        if (!empty($path)) {
            $managerIds = RootManagerInterface::getAllowedManagerIds(array('path' => $path));
            $where[] = array('id', 'IN', $managerIds);
        }
        if (!empty($includePath)) {
            $managerIds = RootManagerInterface::getIncludeManagerIds(array('path' => $includePath));
            $where[] = array('id', 'IN', $managerIds);
        }
        $offset = ($page-1)*$pageSize;
        $managerList = RootManagerInterface::getList(array(
            'where'     => $where,
            'limit'     => $pageSize,
            'offset'    => $offset,
        ));
        $allCount = RootManagerInterface::getCount($where);

        $userList    = array();
        $pathHash    = array();
        if (!empty($managerList)) {
            $userIds = array_column($managerList, 'user_id');
            $userList = UserCommonInterface::getById(array( 'id' => $userIds ));
            $userList = Arr::listToHash('id', $userList);

            // 获取权限列表
            $managerIds = array_column($managerList, 'id');
            $pathHash = RootManagerInterface::getPaths(array(
                'id'    => $managerIds,
            ));
        }

        // 找出invalid path
        $invalidHash = array();
        foreach ($pathHash as $id => $pathSet) {
            foreach ($pathSet as $tmpPath) {
                if (array_key_exists($tmpPath, $invalidHash)) {
                    continue;
                }
                $invalidHash[$tmpPath] = RootPermissionInterface::findPath(array('path' => $tmpPath)) ? 0 : 1;
            }
        }

        // 缓存部分的html
        $html = array();
        $html['pager'] = $this->view->fetch(array(
            'renderAllCount' => $allCount,
            'renderPageSize' => $pageSize,
            'renderRadius'   => 8,
        ), 'widget/pager.php');

        $this->renderFramework(array(
            'html'          => $html,
            'managerList'   => $managerList,
            'userList'      => $userList,
            'pathHash'      => $pathHash,
            'invalidHash'   => $invalidHash,
        ), 'manager/list.php');
    }

    public function ajaxEnableAction() {

        $managerId = Request::getPOST('manager-id', 0);

        // 校验
        $managerInfo = RootManagerInterface::getById(array('id' => $managerId));
        if (empty($managerInfo)) {
            $this->renderAjax(1, '信息不存在！');
        }
        if (!$managerInfo['forbidden']) {
            $this->renderAjax(1, '管理员状态正常！');
        }
        RootManagerInterface::enable(array('id' => $managerId));
        $this->renderAjax(0);
    }

    public function ajaxForbidAction() {

        $managerId = Request::getPOST('manager-id', 0);

        // 校验
        $managerInfo = RootManagerInterface::getById(array('id' => $managerId));
        if (empty($managerInfo)) {
            $this->renderAjax(1, '信息不存在！');
        }
        if ($managerInfo['forbidden']) {
            $this->renderAjax(1, '管理员已禁用！');
        }
        RootManagerInterface::forbid(array('id' => $managerId));
        $this->renderAjax(0);
    }

    public function ajaxDeleteAction() {

        $managerId = Request::getPOST('manager-id', 0);

        // 校验
        $managerInfo = RootManagerInterface::getById(array('id' => $managerId));
        if (empty($managerInfo)) {
            $this->renderAjax(1, '信息不存在！');
        }

        // 删除
        RootManagerInterface::deleteById(array('id' => $managerId));
        $this->setNotice(FrameworkVars::NOTICE_SUCCESS, '删除成功！');
        $this->renderAjax(0);
    }

}
