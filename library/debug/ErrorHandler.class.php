<?php

class ErrorHandler {

    // 处理捕获的错误
    // PHP错误不需要友好处理，如果是ajax请求，那么解析404页面的时候会调用error函数
    public static function logError($type, $msg, $file, $line) {

        $str = LogVars::$friendlyErrorType[$type] . ': ' . $msg . ' ' . $file . ':' . $line;
        if (in_array($type, LogVars::$warnType)) {
            Logger::warn('handle', $str);
        } else if (in_array($type, LogVars::$errorType)) {
            Logger::error('handle', $str);
            Url::redirect404();
        } else if (in_array($type, LogVars::$noticeType)) {
            Logger::notice('handle', $str);
        } else {
            Logger::fatal('handle', $str);
            Url::redirect404();
        }

        return true;
    }

    // 处理未能捕获的错误
    public static function logParseError() {

        $e = error_get_last();

        // 捕获到没有被处理的错误
        if ($e) {
            self::logError($e['type'], $e['message'], $e['file'], $e['line']);
        }
    }

    // 处理异常
    public static function logException($e) {

        // 如果不是BaseException，那么由handle捕获
        if (!is_subclass_of($e, 'BaseException')) {
            Logger::error('handle', $e);
        }

        if (class_exists('Router', false) && Router::$IS_AJAX) {
            $content = array(
                'errorCode'     => E_USER_ERROR,
                'errorMessage'  => '服务器繁忙，请稍后再试！',
            );
            Response::output($content, 'json', Router::$CALLBACK);
            exit;
        } else {
            Url::redirect404();
        }
    }

}
