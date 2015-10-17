<?php

class KeController extends ProjectController {

    // 允许的文件类型
    private static $ALLOWED = array(
        'image' => array('gif', 'jpg', 'jpeg', 'png', 'bmp', 'ico'),
        'flash' => array('swf', 'flv'),
        'media' => array('swf', 'flv', 'mp3', 'wav', 'wma', 'wmv', 'mid', 'avi', 'mpg', 'asf', 'rm', 'rmvb'),
        'file'  => array('doc', 'docx', 'xls', 'xlsx', 'ppt', 'htm', 'html', 'txt', 'zip', 'rar', 'gz', 'bz2'),
    );

    public function ajaxAction() {

        // 获取dir参数
        $dir = Request::getGET('dir', 'image');
        if (!array_key_exists($dir, self::$ALLOWED)) {
            echo json_encode(array('error' => 1, 'message' => '参数错误！'));
            return;
        }

        // 获取上传的文件
        $field = 'imgFile';
        $fileSize = Upload::getFilesize($field);
        $tmpName  = Upload::getTmpName($field);
        $fileExt  = Upload::getFileExt($field);
        if (empty($fileSize)) {
            echo json_encode(array('error' => 1, 'message' => '请上传文件！'));
            return;
        }

        // 校验格式
        if (!in_array($fileExt, self::$ALLOWED[$dir])) {
            echo json_encode(array('error' => 1, 'message' => '文件格式不支持，无法上传！'));
            return;
        }

        // 保存
        $cdnKey = Cdn::uploadLocalFile($tmpName, $this->loginUserInfo['id'], $fileExt);
        $url = Cdn::getUrl($cdnKey);
        echo json_encode(array('error' => 0, 'url' => $url));
    }
}
