<?php

abstract class ProjectController extends BaseController {

    protected $contestInfo    = array();    // 当前比赛信息
    protected $applyInfo      = array();    // 当前用户报名信息
    protected $password       = '';         // 当前用户输入的比赛密码
    protected $isContestAdmin = false;      // 当前用户是否是比赛管理员

    public function __construct() {

        parent::__construct();

        // 校验登陆
        if (empty($this->loginUserInfo)) {
            $this->login();
        }

        // 获取$contestId
        $contestId = (int) Request::getREQUEST('contest-id', 0);
        if (empty($contestId)) {
            $this->render404('比赛ID不存在！');
        }

        // 获取$contestInfo
        $this->contestInfo = OjContestInterface::getDetail(array('id' => $contestId));
        if (empty($this->contestInfo) || $this->contestInfo['hidden']) {
            $this->render404('比赛不存在！');
        }

        if ($this->contestInfo['type'] == ContestVars::TYPE_APPLY) {
            $this->applyInfo = OjContestApplyInterface::getDetail(array(
                'contest_id'    => $contestId,
                'user_id'       => $this->loginUserInfo['id'],
            ));
        }

        // 管理员
        $isOjAdmin = RootCommonInterface::allowed(array(
            'user_id'   => $this->loginUserInfo['id'],
            'path'      => '/hqoj/admin',
        ));
        if ($isOjAdmin || $this->contestInfo['user_id'] == $this->loginUserInfo['id']) {
            $this->isContestAdmin = true;
        }

        // 如果未注册，未输入密码，比赛未开始，那么跳转到比赛首页
        if (Router::$CONTROLLER != 'index') {
            if ($this->contestInfo['type'] == ContestVars::TYPE_APPLY) {
                if (empty($this->applyInfo) || $this->applyInfo['status'] != ContestVars::APPLY_ACCEPTED) {
                    $this->setNotice('error', '您未通过报名！');
                    $url = '/?contest-id=' . $contestId;
                    Url::redirect($url);
                }
            } else if ($this->contestInfo['type'] == ContestVars::TYPE_PASSWORD) {
                if ($this->password != $this->contestInfo['password']) {
                    $this->setNotice('error', '请输入密码！');
                    $url = '/?contest-id=' . $contestId;
                    Url::redirect($url);
                }
            }
            if (time() < $this->contestInfo['begin_time']) {
                $this->setNotice('error', '比赛未开始！');
                $url = '/?contest-id=' . $contestId;
                Url::redirect($url);
            }
        }

        $this->view->assign(array(
            'contestInfo'       => $this->contestInfo,
            'applyInfo'         => $this->applyInfo,
            'password'          => $this->password,
            'isContestAdmin'    => $this->isContestAdmin,
        ));
    }

    protected function renderFramework($params, $tpl) {
        $content = $this->view->fetch($params, $tpl);
        parent::renderFramework(array(
            'frameworkContent'  => $content,
        ), __DIR__ . '/template/framework.php');
    }

    protected function renderFrameworkError($errorMessage = '你要访问的页面不存在！') {
        $this->renderFramework(array(
            'errorMessage'  => $errorMessage,
        ), __DIR__ . '/template/framework_error.php');
    }

}