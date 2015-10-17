<?php

class Router {

    public static $CONTROLLER   = ''; // URL控制层名，如：shop_list, shop_add
    public static $ACTION       = ''; // URL方法名，如：show, ajaxSubmit
    public static $IS_AJAX      = false;
    public static $IS_IFRAME    = false;
    public static $CONTENT_TYPE = 'html';
    public static $CALLBACK     = ''; // jsonp回调方法名
    public static $CLASS_NAME   = ''; // url对应的控制类名，如：IndexController
    public static $CLASS_DIR    = ''; // 如果文件在setup/uc/UpdateController，那么为：setup/uc，如果没有文件夹，那么为''

    protected static function parseUrl() {

        // 通过Rewrite得到，实现index.php单一入口
        $url = Request::getGET('_sys_url_path', '');
        $paths = explode('/', trim($url, '/'));

        foreach ($paths as $key => $value) {
            if ('' === $value) {
                unset($paths[$key]);
                continue;
            }
        }

        $paths = array_slice($paths, 0, 2);

        if (count($paths) == 0){
            $paths[] = 'index';
        }
        if (count($paths) == 1){
            $paths[] = 'default';
        }

        self::$CONTROLLER   = $paths[0];
        self::$ACTION       = $paths[1];

        // 校验controller
        if (!preg_match('/^[a-z][a-zA-z0-9_]*$/', self::$CONTROLLER)) {
            throw new FrameworkException('Url不合法1！');
        }
        // 校验action
        if (!preg_match('/^[a-zA-z_][a-zA-z0-9_]*$/', self::$ACTION)) {
            throw new FrameworkException('Url不合法2！');
        }

        self::$IS_AJAX      = (0 === strpos(self::$ACTION, 'ajax') ? true : false);
        self::$IS_IFRAME    = (0 === strpos(self::$ACTION, 'iframe') ? true : false);
        self::$CONTENT_TYPE = self::$IS_AJAX ? 'json' : 'html';
        self::$CALLBACK     = Request::getGET('callback', '');

        // DIR & CLASS
        $pos = strrpos(self::$CONTROLLER, '_');
        if ($pos === strlen(self::$CONTROLLER)-1) {
            throw new FrameworkException('Url不合法！');
        }
        self::$CLASS_DIR    = (false === $pos) ? '' : str_replace('_', '/', substr(self::$CONTROLLER, 0, $pos-strlen(self::$CONTROLLER)));
        self::$CLASS_NAME   = (false === $pos) ? ucfirst(self::$CONTROLLER).'Controller' : ucfirst(substr(self::$CONTROLLER, $pos+1)).'Controller';
    }

    public static function run() {

        header('Content-type: text/html; charset=utf-8');

        // debug
        Debug::p('PHP Begin');

        $htmlDebug = Request::getGET('htmldebug');
        $ajaxDebug = Request::getGET('ajaxdebug');
        if ($htmlDebug == 'on') {
            Cookie::set('htmldebug', 1);
        }
        if ($htmlDebug == 'off') {
            Cookie::delete('htmldebug');
        }
        if ($ajaxDebug == 'on') {
            Cookie::set('ajaxdebug', 1);
        }
        if ($ajaxDebug == 'off') {
            Cookie::delete('ajaxdebug');
        }

        self::parseUrl();

        $path = empty(self::$CLASS_DIR) ?
            PROJECT_PATH . '/controller/' . self::$CLASS_NAME . '.class.php' :
            PROJECT_PATH . '/controller/' . self::$CLASS_DIR . '/' . self::$CLASS_NAME . '.class.php';

        if (!is_file($path)) {
            $userAgent = Arr::get('HTTP_USER_AGENT', $_SERVER, '');
            throw new FrameworkException("控制器：{$path} 不存在！User Agent: {$userAgent}");
        }

        require_once $path;
        $obj = new self::$CLASS_NAME;
        $actionName = self::$ACTION . 'Action';
        $obj->$actionName();

        // debug
        Debug::p('PHP End');

        if (isset($_COOKIE['htmldebug']) && !self::$IS_AJAX
        || isset($_COOKIE['ajaxdebug']) && self::$IS_AJAX) {
            Debug::show();
        }

    }
}