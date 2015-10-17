<?php

class IndexController extends ProjectController {

    public function defaultAction() {

        $this->renderFramework(array(), 'index/index.php');
    }
}