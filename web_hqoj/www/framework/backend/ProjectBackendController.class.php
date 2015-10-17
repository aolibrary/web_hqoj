<?php

abstract class ProjectBackendController extends ProjectController {

    protected $backendMenuList      = array();  // 当前用户可以访问的项目菜单
    protected $backendMenuInfo      = array();  // 当前菜单

    public function __construct() {

        parent::__construct();

        $this->checkMenu();
    }

    // 菜单权限
    private function checkMenu() {

        // 根据Router::$CLASS_DIR确定菜单
        if (empty(Router::$CLASS_DIR)) {
            throw new ControllerException('无法确定菜单！');
        }

        $arr = explode('/', Router::$CLASS_DIR);
        $menuFile = __DIR__ . '/menu/' . $arr[0] . '.menu.inc.php';


        if (!is_file($menuFile)) {
            throw new ControllerException("菜单{$menuFile}不存在！");
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

}