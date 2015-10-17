<?php

class LogoutController extends ProjectController {

    public function defaultAction() {

        $backUrl = Request::getGET('back-url', '//www.hqoj.net/');
        UcUserInterface::logout();
        Url::redirect($backUrl);
    }

}