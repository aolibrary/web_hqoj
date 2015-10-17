<?php

class PojJudger {

    // 用户名，密码，cookie路径
    private $username   = '';
    private $password   = '';
    private $cookieFile = '';

    private $solutionId = 0;
    private $uid        = 0;
    private $ch         = null;

    public function __construct($solutionId, $uid) {

        $accountInfo      = JudgerConfig::$POJ_ACCOUNT[$uid];
        $this->username   = $accountInfo[0];
        $this->password   = $accountInfo[1];
        $this->cookieFile = __DIR__ . '/cookie/poj_' . $accountInfo[0];
        $this->solutionId = $solutionId;
        $this->uid        = $uid;

        $this->ch = new Curl();
        $this->ch->setOption(array( CURLOPT_HEADER => true ));
        $this->ch->setCookieFile($this->cookieFile);

        $this->login();
    }

    private function __clone() {}

    private function login() {

        $loginData = array(
            'user_id1'  => $this->username,
            'password1' => $this->password,
        );

        // 登录
        $ret = $this->ch->login(StatusVars::POJ_LOGIN_URL, $loginData);
        if (false === $ret || $this->ch->error()) {
            Logger::error('judge', "SOLUTION_ID：{$this->solutionId}，POJ登陆失败！");
            throw new Exception("SOLUTION_ID：{$this->solutionId}，POJ登陆失败！");
        }
    }

    public function run() {

        $solutionInfo = OjSolutionInterface::getDetail(array( 'id' => $this->solutionId ));
        if (empty($solutionInfo)) {
            Logger::error('judge', "SOLUTION_ID：{$this->solutionId}，Solution不存在！");
            throw new Exception("SOLUTION_ID：{$this->solutionId}，Solution不存在！");
        }
        if ($solutionInfo['remote'] != StatusVars::REMOTE_POJ) {
            Logger::error('judge', "SOLUTION_ID：{$this->solutionId}，必须是POJ的Solution！");
            throw new Exception("SOLUTION_ID：{$this->solutionId}，必须是POJ的Solution！");
        }

        // post提交
        $postData = array();
        $postData['problem_id'] = $solutionInfo['problem_id'];
        $postData['language']   = StatusVars::$pojLangMap[$solutionInfo['language']];
        $postData['source']     = $solutionInfo['source'];
        $postData['submit']     = 'Submit';
        $postData['encoded']    = 0;
        $content = $this->ch->post(StatusVars::POJ_SUBMIT_URL, $postData);
        if (!preg_match('/Location: http:\/\/poj.org\/status/sU', $content)) {
            Logger::error('judge', "SOLUTION_ID：{$this->solutionId}，CURL提交代码到POJ失败！CURL耗时：{$this->ch->getLastExecTime()}MS");
            $data = array(
                'id'            => $this->solutionId,
                'result'        => StatusVars::TIME_OUT,
                'time_cost'     => 0,
                'memory_cost'   => 0,
            );
            OjSolutionInterface::save($data);
            Logger::info('judge', "SOLUTION_ID：{$this->solutionId}，提交到POJ失败，写入TIME_OUT");
            return;
        } else {
            Logger::info('judge', "SOLUTION_ID：{$this->solutionId}，CURL成功提交代码到POJ！CURL耗时：{$this->ch->getLastExecTime()}MS");
        }

        // 提交后马上获取runId
        $rowInfo = $this->getResult();

        // 获取获取结果超时
        if (false === $rowInfo) {
            Logger::info('judge', "SOLUTION_ID：{$this->solutionId}，获取结果失败1，写入TIME_OUT");
            $data = array(
                'id'            => $this->solutionId,
                'result'        => StatusVars::TIME_OUT,
                'time_cost'     => 0,
                'memory_cost'   => 0,
            );
            OjSolutionInterface::save($data);
            return;
        } else {
            Logger::info('judge', "SOLUTION_ID：{$this->solutionId}，POJ成功获取RUN_ID！");
            if (in_array($rowInfo['result'], StatusVars::$pojResultMap)) {
                Logger::info('judge', "SOLUTION_ID：{$this->solutionId}，POJ远程judge成功！");
            }

            $trans = new Trans(DbConfig::$SERVER_TRANS);
            $trans->begin();

            $data = array(
                'trans'         => $trans,
                'id'            => $this->solutionId,
                'result'        => $rowInfo['result'],
                'time_cost'     => $rowInfo['time_cost'],
                'memory_cost'   => $rowInfo['memory_cost'],
                'run_id'        => $rowInfo['run_id'],
                'remote_uid'    => $this->uid,
            );
            OjSolutionInterface::save($data);

            // 保存Log
            $dataLog = array(
                'trans'         => $trans,
                'solution_id'   => $this->solutionId,
                'ce'            => Arr::get('ce', $rowInfo['judge_log'], ''),
                're'            => Arr::get('re', $rowInfo['judge_log'], ''),
            );
            OjSolutionLogInterface::save($dataLog);
            $trans->commit();
        }

        if (!in_array($rowInfo['result'], StatusVars::$pojResultMap)) {
            $this->sync();
        }
    }

    /**
     * 同步数据到oj_solution
     *
     * @throws  Exception
     */
    public function sync() {

        $solutionInfo = OjSolutionInterface::getById(array( 'id' => $this->solutionId, ));

        if (empty($solutionInfo)) {
            Logger::error('judge', "SOLUTION_ID：{$this->solutionId}，Solution不存在！");
            throw new Exception("SOLUTION_ID：{$this->solutionId}，Solution不存在！");
        }
        if ($solutionInfo['remote'] != StatusVars::REMOTE_POJ) {
            Logger::error('judge', "SOLUTION_ID：{$this->solutionId}，必须是POJ的Solution！");
            throw new Exception("SOLUTION_ID：{$this->solutionId}，必须是POJ的Solution！");
        }
        if (empty($solutionInfo['run_id'])) {
            Logger::error('judge', "RUN_ID为0！");
            throw new Exception("SOLUTION_ID：{$this->solutionId}，RUN_ID为0！");
        }
        if ($solutionInfo['remote_uid'] != -1 && $solutionInfo['remote_uid'] != $this->uid) {
            Logger::error('judge', "SOLUTION_ID：{$this->solutionId}，当前uid不等于remote_uid，无法获取其他用户的状态！");
            throw new Exception("SOLUTION_ID：{$this->solutionId}，当前uid不等于remote_uid，无法获取其他用户的状态！");
        }

        // 尝试多次获取结果
        $rowInfo = array();
        $i = 1;
        while ($i <= 10) {
            $rowInfo = $this->getResult($solutionInfo['run_id']);
            if (false === $rowInfo || in_array($rowInfo['result'], StatusVars::$pojResultMap)) {
                break;
            }
            $i > 5 ? sleep(2) : usleep(500000);
            $i++;
        }

        // 获取获取结果超时
        if (false === $rowInfo) {
            Logger::info('judge', "SOLUTION_ID：{$this->solutionId}，获取结果失败2，写入TIME_OUT");
            $data = array(
                'id'            => $this->solutionId,
                'result'        => StatusVars::TIME_OUT,
                'time_cost'     => 0,
                'memory_cost'   => 0,
            );
            OjSolutionInterface::save($data);
        } else {
            Logger::info('judge', "SOLUTION_ID：{$this->solutionId}，POJ远程judge成功！尝试次数：{$i}");

            $trans = new Trans(DbConfig::$SERVER_TRANS);
            $trans->begin();

            $data = array(
                'trans'         => $trans,
                'id'            => $this->solutionId,
                'result'        => $rowInfo['result'],
                'time_cost'     => $rowInfo['time_cost'],
                'memory_cost'   => $rowInfo['memory_cost'],
            );
            OjSolutionInterface::save($data);

            // 保存Log
            $dataLog = array(
                'trans'         => $trans,
                'solution_id'   => $this->solutionId,
                'ce'            => Arr::get('ce', $rowInfo['judge_log'], ''),
                're'            => Arr::get('re', $rowInfo['judge_log'], ''),
            );
            OjSolutionLogInterface::save($dataLog);
            $trans->commit();
        }
    }

    /**
     * 获取某个用户的第一条result，解析
     *
     * @param   int     $runId      对方OJ上得runId；如果runId为0，那么取第一条
     * @return  array|false     如果评判还没完成，那么返回空数组，否则返回格式化后的array
     *                          如果发生错误，返回false;
     */
    private function getResult($runId = 0) {

        $url = StatusVars::POJ_STATUS_URL . $this->username;
        if (!empty($runId)) {
            $url = $url . '&top=' . ($runId+1);
        }
        $html = $this->ch->get($url);
        if (empty($html) || $this->ch->error()) {
            Logger::error('judge', "SOLUTION_ID：{$this->solutionId}，CURL 获取Status页面失败，RUNID：{$runId}，URL：{$url}");
            return false;
        }

        // 获取最新的一行
        $matches = array();
        $pattern = '/<form method=get action=status>.*<tr align=center>(.*)<\/tr>/sU';
        if (!preg_match($pattern, $html, $matches)) {
            Logger::error('judge', "SOLUTION_ID：{$this->solutionId}，正则匹配Status第一行失败，RUNID：{$runId}，URL：{$url}");
            return false;
        }

        // 得到行中的每个单元
        $row = $matches[1];
        $pattern = '/<td[^>]*>(.*)<\/td>/sU';
        preg_match_all($pattern, $row, $matches);
        $rowInfo = $matches[1];

        // CHECK $rowInfo
        if (empty($rowInfo) || $runId && $rowInfo[0] != $runId || count($rowInfo) != 9) {
            Logger::error('judge', "SOLUTION_ID：{$this->solutionId}，正则匹配行中单元格失败，RUNID：{$runId}，URL：{$url}");
            return false;
        }

        // 获取result，去除标签
        $result = trim(preg_replace('/<[^>]*>/', '', $rowInfo[3]));

        // 得到状态信息并且格式化
        $solutionInfo = array();
        if (!array_key_exists($result, StatusVars::$pojResultMap)) {
            $solutionInfo['result'] = StatusVars::RUNNING;
        } else {
            $solutionInfo['result'] = StatusVars::$pojResultMap[$result];
        }
        $solutionInfo['result_format']  = $result;
        $solutionInfo['run_id']         = $rowInfo[0];
        $solutionInfo['time_cost']      = (int) $rowInfo[5];
        $solutionInfo['memory_cost']    = (int) $rowInfo[4];

        // 获取调试信息
        $solutionInfo['judge_log'] = array();
        if ($solutionInfo['result'] == StatusVars::COMPILATION_ERROR) {
            $re = $this->getCompileError($solutionInfo['run_id']);
            $solutionInfo['judge_log']['ce'] = (empty($re) ? '' : $re);
        }

        return $solutionInfo;
    }

    private function getCompileError($runId) {

        $url = StatusVars::POJ_COMPILE_INFO_URL . $runId;
        $html = $this->ch->get($url);
        if (false === $html) {
            Logger::error('judge', "SOLUTION_ID：{$this->solutionId}，获取Compile页面失败，CURL耗时：{$this->ch->getLastExecTime()}，RUNID：{$runId}，URL：{$url}");
            return false;
        }

        // 得到结果
        $matches = array();
        $pattern = '/Compile Error.*<pre>(.*)<\/pre>/sU';
        if (!preg_match($pattern, $html, $matches)) {
            Logger::error('judge', "SOLUTION_ID：{$this->solutionId}，正则匹配Compiation失败，RUNID：{$runId}，URL：{$url}");
            return false;
        }
        return $matches[1];
    }
}
