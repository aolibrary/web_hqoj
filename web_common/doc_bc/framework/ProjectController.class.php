<?php

abstract class ProjectController extends BackendController {

    public function __construct() {

        parent::__construct();

        $menuArr = array();
        foreach (DocVars::$CATEGORY as $i => $title) {
            $menuItem = array(
                'title' => $title,
                'url'   => '/list/' . $i . '/',
            );
            $menuArr[] = $menuItem;
            if ($menuItem['url'] == Url::getPath()) {
                $this->backendMenuInfo = $menuItem;
                $this->backendMenuInfo['parent_title'] = $this->backendMenuList['category']['title'];
                $this->backendMenuInfo['parent_code']  = Arr::get('code', $this->backendMenuList['category'], '');
            }
        }
        $this->backendMenuList['category']['menu'] = $menuArr;

        $this->view->assign(array(
            'backendMenuList'   => $this->backendMenuList,
            'backendMenuInfo'   => $this->backendMenuInfo,
        ));
    }
}