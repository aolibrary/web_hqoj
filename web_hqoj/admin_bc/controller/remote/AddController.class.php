<?php

class AddController extends ProjectController {

    public function defaultAction() {

        $this->renderFramework(array(), 'remote/add.php');
    }

    public function ajaxLoadProblemAction() {

        require_once INCLUDE_PATH . '/remote/RemoteProblemApi.class.php';

        $remote = (int) Request::getPOST('remote');
        $number = (int) Request::getPOST('number');

        if (empty($number)) {
            $this->renderError('参数错误1！');
        }

        if (!array_key_exists($remote, StatusVars::$REMOTE_SCHOOL) || $remote == StatusVars::REMOTE_HQU) {
            $this->renderError('参数错误2！');
        }


        // 获取各个OJ下题号
        $where = array(
            'group_by'  => 'remote',
        );
        $problemList = OjProblemInterface::getList(array(
            'field' => 'remote, max(problem_code) AS max_id',
            'where' => $where,
        ));
        if (false === $problemList) {
            $this->renderError('查询失败！');
        }

        $remoteHash = Arr::listToHash('remote', $problemList);
        foreach ($problemList as $problemInfo) {
            $remoteHash[$problemInfo['remote']] = $problemInfo['max_id'];
        }

        $hduFromId = (int) Arr::get(StatusVars::REMOTE_HDU, $remoteHash, 999) + 1;
        $pojFromId = (int) Arr::get(StatusVars::REMOTE_POJ, $remoteHash, 999) + 1;
        $zojFromId = (int) Arr::get(StatusVars::REMOTE_POJ, $remoteHash, 1000) + 1;

        $loadProblemList = array();
        if ($remote == StatusVars::REMOTE_HDU) {
            $loadProblemList = RemoteProblemApi::getProblemList(StatusVars::REMOTE_HDU, $hduFromId, $number);
        } else if ($remote == StatusVars::REMOTE_POJ) {
            $loadProblemList = RemoteProblemApi::getProblemList(StatusVars::REMOTE_POJ, $pojFromId, $number);
        } else if ($remote == StatusVars::REMOTE_ZOJ) {
            $loadProblemList = RemoteProblemApi::getProblemList(StatusVars::REMOTE_ZOJ, $zojFromId, $number);
        }

        foreach ($loadProblemList as &$problemInfo) {
            $problemInfo['remote'] = $remote;
            $problemInfo['remote_format'] = StatusVars::$REMOTE_SCHOOL[$remote];
        }

        $this->renderAjax(0, 'Success!', array('problemList' => $loadProblemList));

    }

    public function ajaxAddProblemAction() {

        $problemJson = Request::getPOST('problem-json');

        $problemList = json_decode($problemJson);
        if (empty($problemList)) {
            $this->renderError('参数错误2！');
        }

        $dataList = array();
        foreach ($problemList as $problemInfo) {
            $problemInfo = (array) $problemInfo;

            // 如果title为空，那么不插入
            if (empty($problemInfo['problem_id']) || empty($problemInfo['problem_code']) || empty($problemInfo['title'])) {
                continue;
            }

            $data = array();
            $data['remote']       = $problemInfo['remote'];
            $data['problem_id']   = $problemInfo['problem_id'];
            $data['problem_code'] = $problemInfo['problem_code'];
            $data['title']        = $problemInfo['title'];
            $data['source']       = $problemInfo['source'];
            $data['user_id']      = $this->loginUserInfo['id'];
            $data['hidden']       = empty($data['title']) ? 1 : 0;
            $dataList[] = $data;
        }
        OjProblemInterface::insertAll($dataList);
        $this->renderAjax(0);
    }
}
