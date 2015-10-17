<?php

class DataManagerController extends ProjectController {

    public function watchAction() {

        $problemId = Request::getGET('problem-id');
        $file = Request::getGET('file');

        // 处理文件名
        if (!$this->checkFile($file) || empty($problemId)) {
            Response::output('参数错误！', 'html');
            return;
        }

        // 删除文件
        $path = '/home/judge/data/' . $problemId . '/' . $file;

        // 是否存在
        if (!is_file($path)) {
            Response::output('文件不存在！', 'html');
            return;
        }

        $content = file_get_contents($path);
        $content = htmlspecialchars($content);
        $content = "<pre>{$content}</pre>";
        Response::output($content, 'html');
    }

    public function iframeManagerAction() {

        $problemId = Request::getGET('problem-id');

        // 校验权限
        $problemInfo = OjProblemInterface::getDetail(array(
            'remote'     => StatusVars::REMOTE_HQU,
            'problem_id' => $problemId,
        ));
        if (empty($problemInfo)) {
            $this->renderError('题目不存在！');
        }

        // 创建文件夹
        $dataDir = '/home/judge/data/' . $problemId . '/';
        $dataUploadDir = $dataDir . '/upload/';
        if (!file_exists($dataUploadDir)) {
            mkdir($dataUploadDir, 0777, true);
        }

        // 获取使用中的文件列表
        if (false == ($handle = opendir($dataDir))) {
            $this->renderError('打开data文件夹失败！');
        }
        $fileList = array();
        while (false !== ($filename = readdir($handle))) {
            $file = $dataDir . '/' . $filename;
            if ($filename{0} == '.' || is_dir($file)) {
                continue;
            }
            $fileInfo = array();
            $fileInfo['filename'] = $filename; //文件名，包含扩展名
            $fileInfo['datetime'] = date('Y-m-d H:i:s', filemtime($file)); //文件最后修改时间
            $fileInfo['filesize'] = filesize($file);
            $fileList[] = $fileInfo;
        }
        closedir($handle);

        // 获取所有的文件列表
        if (false == ($handle = opendir($dataUploadDir))) {
            $this->renderError('打开upload文件夹失败！');
        }
        $uploadFileList = array();
        while (false !== ($filename = readdir($handle))) {
            $file = $dataUploadDir . '/' . $filename;
            if ($filename{0} == '.' || is_dir($file)) {
                continue;
            }
            $fileInfo = array();
            $fileInfo['filename'] = $filename; //文件名，包含扩展名
            $fileInfo['datetime'] = date('Y-m-d H:i:s', filemtime($file)); //文件最后修改时间
            $fileInfo['filesize'] = filesize($file);
            $uploadFileList[] = $fileInfo;
        }
        closedir($handle);

        // 排序
        function cmpFunc($a, $b) {
            return strcmp($a['filename'], $b['filename']);
        }
        usort($fileList, 'cmpFunc');
        usort($uploadFileList, 'cmpFunc');

        // 计算大小，并格式化
        $fileSize = 0;
        foreach ($fileList as $key => $fileInfo) {
            $fileList[$key]['format'] = sprintf('%-14s %10d %s', $fileInfo['filename'], $fileInfo['filesize'], $fileInfo['datetime']);
            $fileList[$key]['format'] = str_replace(' ', '&nbsp;', $fileList[$key]['format']);
            $fileSize += $fileList[$key]['filesize'];
        }

        $uploadFileSize = 0;
        foreach ($uploadFileList as $key => $fileInfo) {
            $uploadFileList[$key]['format'] = sprintf('%-14s %10d %s', $fileInfo['filename'], $fileInfo['filesize'], $fileInfo['datetime']);
            $uploadFileList[$key]['format'] = str_replace(' ', '&nbsp;', $uploadFileList[$key]['format']);
            $uploadFileSize += $uploadFileList[$key]['filesize'];
        }

        $this->renderIframe(array(
            'fileList'          => $fileList,
            'uploadFileList'    => $uploadFileList,
            'problemInfo'       => $problemInfo,
            'fileSize'          => $fileSize,
            'uploadFileSize'    => $uploadFileSize,
        ), 'problem/iframe/data_manager.php');
    }

    private function checkFile($fileParam) {

        if (empty($fileParam)) {
            return false;
        }
        $fileArr = explode('/', $fileParam);
        if (count($fileArr) == 1
            || $fileArr[0] == 'upload' && count($fileArr) == 2) {
            return true;
        }
        return false;
    }

    public function ajaxRemoveAction() {

        $problemId = Request::getPOST('problem-id');
        $file      = Request::getPOST('file');

        // 处理文件名
        if (!$this->checkFile($file) || empty($problemId)) {
            $this->renderError('参数错误！');
        }

        // 校验权限
        $problemInfo = OjProblemInterface::getDetail(array(
            'remote'     => StatusVars::REMOTE_HQU,
            'problem_id' => $problemId,
        ));
        if (empty($problemInfo)) {
            $this->renderError('题目不存在！');
        }

        // 删除文件
        $path = '/home/judge/data/' . $problemId . '/' . $file;

        // 是否存在
        if (!is_file($path)) {
            $this->renderError('文件不存在！');
        }

        $ret = unlink($path);
        if (false == $ret) {
            $this->renderError('删除失败！');
        }

        // 记录操作
        $dataTime = date('Y-m-d H:i:s', time());
        $history = "<p>{$dataTime} 管理员{$this->loginUserInfo['username']}删除文件{$file}</p>";
        OjProblemInterface::auditHistory(array(
            'problem_id'     => $problemId,
            'append_history' => $history,
        ));
        $this->renderAjax(0);
    }

    public function ajaxCopyAction() {

        $problemId = Request::getPOST('problem-id');
        $file      = Request::getPOST('file');

        // 处理文件名
        if (!$this->checkFile($file) || empty($problemId) || 0 !== strpos($file, 'upload/')) {
            $this->renderError('参数错误！');
        }

        // 校验权限
        $problemInfo = OjProblemInterface::getDetail(array(
            'remote'     => StatusVars::REMOTE_HQU,
            'problem_id' => $problemId,
        ));
        if (empty($problemInfo)) {
            $this->renderError('题目不存在！');
        }

        // 只能复制in out文件
        $arr = explode('.', $file);
        $ext = end($arr);
        if (!in_array($ext, array('in', 'out'))) {
            $this->renderError('只能复制扩展名为in，out的文件！');
        }

        // 复制文件
        $src = '/home/judge/data/' . $problemId . '/' . $file;
        $dest = '/home/judge/data/' . $problemId . '/' . basename($file);

        // 是否存在
        if (!is_file($src)) {
            $this->renderError('文件不存在！');
        }

        $ret = copy($src, $dest);
        if (false == $ret) {
            $this->renderError('复制失败！');
        }

        // 重新获取data文件夹
        $dataDir = '/home/judge/data/' . $problemId . '/';
        if (false == ($handle = opendir($dataDir))) {
            $this->renderError('打开data文件夹失败！');
        }
        $fileList = array();
        while (false !== ($filename = readdir($handle))) {
            $fullpath = $dataDir . '/' . $filename;
            if ($filename{0} == '.' || is_dir($fullpath)) {
                continue;
            }
            $fileInfo = array();
            $fileInfo['filename'] = $filename; //文件名，包含扩展名
            $fileInfo['datetime'] = date('Y-m-d H:i:s', filemtime($fullpath)); //文件最后修改时间
            $fileInfo['filesize'] = filesize($fullpath);
            $fileList[] = $fileInfo;
        }
        closedir($handle);

        // 排序
        function cmpFunc($a, $b) {
            return strcmp($a['filename'], $b['filename']);
        }
        usort($fileList, 'cmpFunc');

        // 计算大小，并格式化
        $fileSize = 0;
        foreach ($fileList as $key => $fileInfo) {
            $fileList[$key]['format'] = sprintf('%-14s %10d %s', $fileInfo['filename'], $fileInfo['filesize'], $fileInfo['datetime']);
            $fileList[$key]['format'] = str_replace(' ', '&nbsp;', $fileList[$key]['format']);
            $fileSize += $fileList[$key]['filesize'];
        }

        // 记录操作
        $dateTime = date('Y-m-d H:i:s', time());
        $history = "<p>{$dateTime} 管理员{$this->loginUserInfo['username']}复制文件{$file}</p>";
        OjProblemInterface::auditHistory(array(
            'problem_id'     => $problemId,
            'append_history' => $history,
        ));
        $this->renderAjax(0, 'Success!', array('fileList' => $fileList, 'fileSize' => $fileSize));
    }

    /**
     * Uploader组件只接收html
     */
    private function echoJson($errorCode, $errorMessage = 'success', $params = array()) {

        $params['errorCode'] = $errorCode;
        $params['errorMessage'] = $errorMessage;
        echo json_encode($params);
        exit;
    }

    public function ajaxUploadAction() {

        $problemId = Request::getPOST('problem-id');
        $fileList  = Upload::getFileList('file');

        if (empty($fileList) || empty($problemId)) {
            $this->echoJson(1, '参数错误！');
        }

        // 校验权限
        $problemInfo = OjProblemInterface::getDetail(array(
            'remote'     => StatusVars::REMOTE_HQU,
            'problem_id' => $problemId,
        ));
        if (empty($problemInfo)) {
            $this->echoJson(1, '题目不存在！');
        }

        // 获取upload所有文件，并计算大小
        $dataUploadDir = '/home/judge/data/' . $problemId . '/upload/';
        if (false == ($handle = opendir($dataUploadDir))) {
            $this->echoJson(1, '打开upload文件夹失败！');
        }
        $uploadFileSize = 0;
        while (false !== ($filename = readdir($handle))) {
            $file = $dataUploadDir . '/' . $filename;
            if ($filename{0} == '.' || is_dir($file)) {
                continue;
            }
            $uploadFileSize += filesize($file);
        }
        closedir($handle);
        foreach ($fileList as $key => $fileInfo) {
            if (!preg_match('/^[A-Za-z0-9_\.]{1,14}$/', $fileInfo['name'])) {
                $this->echoJson(1, '文件名必须是字母数字下划线组成，并且不能超过14个字符！');
                return;
            }
            $uploadFileSize += $fileInfo['size'];
        }
        if ($uploadFileSize > 5*1024*1024) {
            $this->echoJson(1, '上传目录空间限制5M，你已经超出范围啦！');
        }

        // 移动
        foreach ($fileList as $key => $fileInfo) {
            $path = '/home/judge/data/' . $problemId . '/upload/' . $fileInfo['name'];
            move_uploaded_file($fileInfo['tmp_name'], $path);
        }

        // 重新获取upload文件夹
        if (false == ($handle = opendir($dataUploadDir))) {
            $this->echoJson(1, '打开upload文件夹失败！');
        }
        $uploadFileList = array();
        while (false !== ($filename = readdir($handle))) {
            $file = $dataUploadDir . '/' . $filename;
            if ($filename{0} == '.' || is_dir($file)) {
                continue;
            }
            $fileInfo = array();
            $fileInfo['filename'] = $filename; //文件名，包含扩展名
            $fileInfo['datetime'] = date('Y-m-d H:i:s', filemtime($file)); //文件最后修改时间
            $fileInfo['filesize'] = filesize($file);
            $uploadFileList[] = $fileInfo;
        }
        closedir($handle);

        // 排序
        function cmpFunc($a, $b) {
            return strcmp($a['filename'], $b['filename']);
        }
        usort($uploadFileList, 'cmpFunc');

        // 计算大小，并格式化
        $uploadFileSize = 0;
        foreach ($uploadFileList as $key => $fileInfo) {
            $uploadFileList[$key]['format'] = sprintf('%-14s %10d %s', $fileInfo['filename'], $fileInfo['filesize'], $fileInfo['datetime']);
            $uploadFileList[$key]['format'] = str_replace(' ', '&nbsp;', $uploadFileList[$key]['format']);
            $uploadFileSize += $uploadFileList[$key]['filesize'];
        }

        // 记录操作
        $dateTime = date('Y-m-d H:i:s', time());
        $history = "<p>{$dateTime} 管理员{$this->loginUserInfo['username']}批量上传了文件</p>";
        OjProblemInterface::auditHistory(array(
            'problem_id'     => $problemId,
            'append_history' => $history,
        ));
        $this->echoJson(0, 'success', array('uploadFileList' => $uploadFileList, 'uploadFileSize' => $uploadFileSize));
    }
}
