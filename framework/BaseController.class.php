<?php

abstract class BaseController {

    // 视图对象
    protected $view = null;

    // 登陆的用户，如果为空，说明用户没有登陆
    protected $loginUserInfo = array();

    protected function __construct() {

        $dir = PROJECT_PATH . '/template';
        $this->view = new View($dir);

        // 设置html属性
        $this->setTitle();
        $this->setMeta();

        // 获取登陆用户
        $this->loginUserInfo = UserCommonInterface::getLoginUserInfo();

        $this->view->assign(array(
            'loginUserInfo' => $this->loginUserInfo,
        ));
    }

    protected function login() {

        if (Router::$IS_AJAX || Router::$IS_IFRAME) {
            $this->renderError('请先登录！');
        }
        $backUrl = Url::getCurrentUrl(array('back-url' => null));
        $url = Url::make('//uc.hqoj.net/login/', array( 'back-url' => $backUrl ));
        Url::redirect($url);
    }

    final protected function render($params, $tpl) {

        echo $this->view->fetch($params, $tpl);

        // debug
        Debug::p('PHP End');
        if (isset($_COOKIE['htmldebug']) && !Router::$IS_AJAX) {
            Debug::show();
        }
        exit;
    }

    /**
     * 调用基类renderFramework,renderIframe
     *
     * @param   string  $errorMessage
     */
    final protected function render404($errorMessage = '页面不存在！') {
        if (Router::$IS_AJAX) {
            $this->renderAjax(1, $errorMessage);
        } else if (Router::$IS_IFRAME) {
            self::renderIframeError($errorMessage);
        } else {
            $this->render(array(
                'errorMessage'  => $errorMessage,
            ), __DIR__ . '/template/error.php');
        }
    }

    /**
     * 调用子类renderFramework,renderIframe
     *
     * @param   string  $errorMessage
     */
    final protected function renderError($errorMessage = '页面不存在！') {
        if (Router::$IS_AJAX) {
            $this->renderAjax(1, $errorMessage);
        } else if (Router::$IS_IFRAME) {
            $this->renderIframeError($errorMessage);
        } else {
            $this->renderFrameworkError($errorMessage);
        }
    }

    final protected function renderAjax($errorCode, $errorMessage = '', $otherParams = array()) {
        $otherParams['errorCode']       = $errorCode;
        $otherParams['errorMessage']    = $errorMessage;
        Response::output($otherParams, 'json', Router::$CALLBACK);

        // debug
        Debug::p('PHP End');
        if (isset($_COOKIE['ajaxdebug']) && Router::$IS_AJAX) {
            Debug::show();
        }
        exit;
    }

    protected function renderFramework($params, $tpl) {
        $content = $this->view->fetch($params, $tpl);
        $file = __DIR__ . '/template/framework.php';
        $this->render(array(
            'frameworkContent'  => $content,
        ), $file);
    }

    protected function renderFrameworkError($errorMessage = '页面不存在！') {
        $file = __DIR__ . '/template/framework_error.php';
        $this->renderFramework(array(
            'errorMessage'  => $errorMessage,
        ), $file);
    }

    protected function renderIframe($params, $tpl) {
        $content = $this->view->fetch($params, $tpl);
        $file = __DIR__ . '/template/iframe.php';
        $this->render(array(
            'frameworkContent'  => $content,
        ), $file);
    }

    protected function renderIframeError($errorMessage = '页面不存在！') {
        $file = __DIR__ . '/template/iframe_error.php';
        $this->renderIframe(array(
            'errorMessage'  => $errorMessage,
        ), $file);
    }

    protected function setNotice($type, $message, $timeout = 3) {
        $arr = array(
            'type'      => $type,
            'message'   => $message,
            'timeout'   => $timeout
        );
        return Cookie::set('global_framework_notice', json_encode($arr));
    }

    protected function setMeta($params = array()) {
        $metaList = Arr::filter($params, array(
            'keywords',
            'description',
        ));
        $this->view->assign(array(
            'metaList'  => $metaList,
        ));
    }

    protected function setTitle($title = 'HQOJ') {
        $this->view->assign(array(
            'htmlTitle' => $title,
        ));
    }
}