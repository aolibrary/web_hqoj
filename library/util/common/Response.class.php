<?php

class Response {

    // mimeTypes类型，从文件导入
    public static $mimeTypes = array();

    public static function getMimeTypes() {

        if (empty(self::$mimeTypes)) {
            self::$mimeTypes = include __DIR__ . '/mimeTypes.php';
        }
        return self::$mimeTypes;

    }

    public static function output($content, $contentType = 'html', $callback = '') {

        $mimeTypes = self::getMimeTypes();
        header('Content-Type: ' . $mimeTypes[$contentType] . '; charset=' . GlobalConfig::CONTENT_CHARSET);
        if ($contentType == 'json') {
            if (empty($callback)) {
                echo json_encode($content);
            } else {
                echo $callback . '(' . json_encode($content) . ')';
            }
        } else {
            echo $content;
        }
    }

    /**
     * 作用和output类似，不过这里是以下载的形式输出
     *
     * @param   $content    string  stream
     * @param   $saveAs     string  save name
     */
    public static function download($content, $saveAs = '') {

        header('Content-type: application/octet-stream');
        if (!empty($saveAs)) {
            header('Content-Disposition: attachment; filename=' . $saveAs);
        }
        echo $content;
    }

}