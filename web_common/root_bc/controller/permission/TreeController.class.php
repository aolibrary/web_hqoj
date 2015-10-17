<?php

class TreeController extends ProjectController {

    public function defaultAction() {

        $this->renderFramework(array(

        ), 'permission/tree.php');
    }

    public function ajaxGetJstreeJsonAction() {

        echo RootPermissionInterface::getPermissionTreeJson(array('from_cache' => true));
    }

}
