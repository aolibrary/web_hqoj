<?php

class Upload {

    /**
     * 移动上传的文件
     *
     * @param   $field  string  表单域
     * @param   $file   string  目的路径
     * @return  boolean
     */
    public static function move($field, $file) {

        $tmpFile = self::getTmpName($field);
        if (empty($tmpFile)) {
            return false;
        }
        $dir = dirname($file);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        return move_uploaded_file($tmpFile, $file);
    }

    public static function getTmpName($field) {

        $tmpName  = $_FILES[$field]['tmp_name'];
        if (empty($tmpName)) {
            return false;
        }
        return $tmpName;
    }

    public static function getFilesize($field) {

        $filesize = $_FILES[$field]['size'];
        if (empty($filesize)) {
            return false;
        }
        return $filesize;
    }

    public static function getFilename($field) {

        $filename = $_FILES[$field]['name'];
        if (empty($filename)) {
            return false;
        }
        return $filename;
    }

    public static function getFileExt($field) {

        $filename = $_FILES[$field]['name'];
        if (empty($filename)) {
            return false;
        }
        $arr = explode('.', $filename);
        return end($arr);
    }

    public static function getFileList($field) {

        $postFiles = $_FILES[$field];
        $fileList = array();
        $count = count($postFiles['name']);
        $allKey = array_keys($postFiles);

        for ($i=0; $i<$count; $i++) {
            foreach ($allKey as $key) {
                $fileList[$i][$key] = $postFiles[$key][$i];
            }
        }

        return $fileList;
    }
}
