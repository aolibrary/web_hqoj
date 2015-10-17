<?php

class UpdateController extends ProjectBackendController {

    public function defaultAction() {

        $this->renderFramework(array(), 'setup/uc/update.php');
    }

    private function echoJson($errorCode, $errorMessage, $otherParams = array()) {

        $otherParams['errorCode']       = $errorCode;
        $otherParams['errorMessage']    = $errorMessage;
        echo json_encode($otherParams);
        exit;
    }

    public function ajaxUploadAction() {

        $field = 'file';

        $fileExt    = Upload::getFileExt($field);
        $fileSize   = Upload::getFilesize($field);
        $tmpFile    = Upload::getTmpName($field);

        if (!in_array($fileExt, array('png', 'jpeg', 'jpg', 'gif', 'bmp'))) {
            $this->echoJson(1, '只允许jpg,jpeg,bmp,png,gif格式的图片！');
        }

        if ($fileSize > 102400) {
            $this->echoJson(1, '图片大小不能超过100KB！');
        }

        // 保存头像
        $cdnKey = Cdn::uploadLocalFile($tmpFile, $this->loginUserInfo['id'], $fileExt);

        UserCommonInterface::save(array(
            'id'        => $this->loginUserInfo['id'],
            'head_img'  => $cdnKey,
        ));

        // 删除原先的头像
        Cdn::delete($this->loginUserInfo['head_img']);

        $this->echoJson(0, 'Success!', array('src' => OjCommonHelper::getHeadUrl($cdnKey, $this->loginUserInfo['sex'])));
    }

    public function ajaxSubmitAction() {

        $sex      = Request::getPOST('sex');
        $share    = Request::getPOST('share');
        $motto    = trim(Request::getPOST('motto'));
        $nickname = trim(Request::getPOST('nickname'));

        if (empty($nickname)) {
            $this->renderError('昵称不能为空！');
        }

        if (!in_array($share, array(0, 1)) || !in_array($sex, array(1, 2))) {
            $this->renderError('请填写信息！');
        }

        if (mb_strlen($motto, 'utf8') > 100) {
            $this->renderError('签名不能超过100个字！');
        }

        if (mb_strlen($nickname, 'utf8') > 16) {
            $this->renderError('昵称长度16个字符以内！');
        }

        $data = array(
            'id'        => $this->loginUserInfo['id'],
            'nickname'  => $nickname,
            'motto'     => $motto,
            'sex'       => $sex,
            'share'     => $share,
        );
        UserCommonInterface::save($data);

        $this->renderAjax(0);
    }
}