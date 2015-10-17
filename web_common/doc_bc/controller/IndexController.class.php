<?php

class IndexController extends ProjectController {

    public function defaultAction() {

        $this->renderFramework(array(
            'str' => 'A New Project!',
        ), 'index/index.php');
    }
}