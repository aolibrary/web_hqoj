<?php

abstract class BackendController extends BaseController {

    protected $backendProjectList   = array();  // 当前用户可以访问的项目列表
    protected $backendProjectInfo   = array();  // 当前项目
    protected $backendMenuList      = array();  // 当前用户可以访问的项目菜单
    protected $backendMenuInfo      = array();  // 当前菜单

    protected function __construct() {

        parent::__construct();

        $this->checkLogin();
        $this->checkProject();
        $this->checkMenu();
        $this->setTitle('后台');
    }

    // 校验登陆
    private function checkLogin() {
        if (empty($this->loginUserInfo)) {
            $this->login();
        }
    }

    // 项目权限
    private function checkProject() {

        // 获取全部的项目列表
        $this->backendProjectList = BackendConfig::$PROJECT_LIST;

        // 获取当前项目
        $currentDomain = Url::getDomain();
        foreach ($this->backendProjectList as $projectInfo) {
            if ($currentDomain == Url::getDomain($projectInfo['url'])) {
                $this->backendProjectInfo = $projectInfo;
            }
        }
        if (empty($this->backendProjectInfo)) {
            $this->render404('项目不存在！');
        }

        // 当前项目权限判断
        $code = Arr::get('code', $this->backendProjectInfo, '');
        if (!empty($code)) {
            $allowed = RootCommonInterface::allowed(array(
                'user_id'   => $this->loginUserInfo['id'],
                'path'      => $code,
            ));
            if (!$allowed) {
                $this->render404('你没有权限访问！');
            }
        }

        // 过滤没有权限的项目
        foreach ($this->backendProjectList as $key => $value) {
            $code = Arr::get('code', $value, '');
            if (!empty($code)) {
                $allowed = RootCommonInterface::allowed(array(
                    'user_id'   => $this->loginUserInfo['id'],
                    'path'      => $code,
                ));
                if (!$allowed) {
                    unset($this->backendProjectList[$key]);
                }
            }
        }

        // assign
        $this->view->assign(array(
            'backendProjectList'    => $this->backendProjectList,
            'backendProjectInfo'    => $this->backendProjectInfo,
        ));
    }

    // 菜单权限
    private function checkMenu() {

        $menuFile = __DIR__ . '/menu/' . $this->backendProjectInfo['menu'];
        if (!is_file($menuFile)) {
            $this->backendMenuList = array();
            $this->backendMenuInfo = array();

            // assign
            $this->view->assign(array(
                'backendMenuList'       => $this->backendMenuList,
                'backendMenuInfo'       => $this->backendMenuInfo,
            ));
            return;
        }

        $this->backendMenuList = include $menuFile;

        // 计算当前菜单
        $currentPath = Url::getPath();
        foreach ($this->backendMenuList as $menuInfo) {
            foreach ($menuInfo['menu'] as $menuItem) {
                if ($currentPath == $menuItem['url']) {
                    $this->backendMenuInfo = $menuItem;
                    $this->backendMenuInfo['parent_title'] = $menuInfo['title'];
                    $this->backendMenuInfo['parent_code']  = Arr::get('code', $menuInfo, '');
                    break;
                }
            }
            if (!empty($this->backendMenuInfo)) {
                break;
            }
        }

        // 当前一级菜单和二级菜单权限判断
        if (!empty($this->backendMenuInfo)) {

            // 一级菜单权限
            $code = Arr::get('parent_code', $this->backendMenuInfo, '');
            if (!empty($code)) {
                $allowed = RootCommonInterface::allowed(array(
                    'user_id'   => $this->loginUserInfo['id'],
                    'path'      => $code,
                ));
                if (!$allowed) {
                    $this->render404('你没有权限访问！');
                }
            }

            // 二级菜单权限
            $code = Arr::get('code', $this->backendMenuInfo, '');
            if (!empty($code)) {
                $allowed = RootCommonInterface::allowed(array(
                    'user_id'   => $this->loginUserInfo['id'],
                    'path'      => $code,
                ));
                if (!$allowed) {
                    $this->render404('你没有权限访问！');
                }
            }
        }

        // 过滤没有权限的菜单
        foreach ($this->backendMenuList as $i => $menuInfo) {
            $code = Arr::get('code', $menuInfo, '');
            if (!empty($code)) {
                $allowed = RootCommonInterface::allowed(array(
                    'user_id'   => $this->loginUserInfo['id'],
                    'path'      => $code,
                ));
                if (!$allowed) {
                    unset($this->backendMenuList[$i]);
                    continue;
                }
            }
            foreach ($menuInfo['menu'] as $j => $menuItem) {
                $code = Arr::get('code', $menuItem, '');
                if (!empty($code)) {
                    $allowed = RootCommonInterface::allowed(array(
                        'user_id'   => $this->loginUserInfo['id'],
                        'path'      => $code,
                    ));
                    if (!$allowed) {
                        unset($this->backendMenuList[$i]['menu'][$j]);
                        if (empty($this->backendMenuList[$i]['menu'])) {
                            unset($this->backendMenuList[$i]);
                        }
                        continue;
                    }
                }
            }
        }

        // assign
        $this->view->assign(array(
            'backendMenuList'       => $this->backendMenuList,
            'backendMenuInfo'       => $this->backendMenuInfo,
        ));
    }

    protected function renderFramework($params, $tpl) {
        $content = $this->view->fetch($params, $tpl);
        $file = __DIR__ . '/template/backend.php';
        parent::renderFramework(array(
            'backendContent'  => $content,
        ), $file);
    }

    protected function renderFrameworkError($errorMessage = '你要访问的页面不存在！') {
        $file = __DIR__ . '/template/backend_error.php';
        $this->renderFramework(array(
            'errorMessage'  => $errorMessage,
        ), $file);
    }

}