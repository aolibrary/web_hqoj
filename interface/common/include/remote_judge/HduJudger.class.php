<?php

class HduJudger {

    // 用户名，密码，cookie路径
    private $username   = '';
    private $password   = '';
    private $cookieFile = '';

    private $solutionId = 0;        // 需要处理的solutionId
    private $uid        = 0;        // remote_uid，用来实现分布式
    private $ch         = null;

    public function __construct($solutionId, $uid) {

        $accountInfo      = JudgerConfig::$HDU_ACCOUNT[$uid];
        $this->username   = $accountInfo[0];
        $this->password   = $accountInfo[1];
        $this->cookieFile = __DIR__ . '/cookie/hdu_' . $accountInfo[0];
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
            'username' => $this->username,
            'userpass' => $this->password,
        );

        // 登录
        $ret = $this->ch->login(StatusVars::HDU_LOGIN_URL, $loginData);
        if (false === $ret || $this->ch->error()) {
            Logger::error('judge', "SOLUTION_ID：{$this->solutionId}，HDU登陆失败！");
            throw new Exception("SOLUTION_ID：{$this->solutionId}，HDU登陆失败！");
        }
    }

    public function run() {

        $solutionInfo = OjSolutionInterface::getDetail(array( 'id' => $this->solutionId ));
        if (empty($solutionInfo)) {
            Logger::error('judge', "SOLUTION_ID：{$this->solutionId}，Solution不存在！");
            throw new Exception("SOLUTION_ID：{$this->solutionId}，Solution不存在！");
        }
        if ($solutionInfo['remote'] != StatusVars::REMOTE_HDU) {
            Logger::error('judge', "SOLUTION_ID：{$this->solutionId}，必须是HDU的Solution！");
            throw new Exception("SOLUTION_ID：{$this->solutionId}，必须是HDU的Solution！");
        }

        // post提交
        $postData = array();
        $postData['problemid'] = $solutionInfo['problem_id'];
        $postData['language']  = StatusVars::$hduLangMap[$solutionInfo['language']];
        $postData['usercode']  = iconv('UTF-8', 'GBK', $solutionInfo['source']);
        if (false === $postData['usercode']) {
            Logger::error('judge', "SOLUTION_ID：{$this->solutionId}，代码中含有非法字符，转码失败！");
            $data = array(
                'id'            => $this->solutionId,
                'result'        => StatusVars::INVALID,
                'time_cost'     => 0,
                'memory_cost'   => 0,
            );
            OjSolutionInterface::save($data);
            Logger::info('judge', "SOLUTION_ID：{$this->solutionId}，代码转码失败，写入INVALID");
            return;
        }
        $content = $this->ch->post(StatusVars::HDU_SUBMIT_URL, $postData);
        if (!preg_match('/Location: status.php/sU', $content)) {
            Logger::error('judge', "SOLUTION_ID：{$this->solutionId}，CURL提交代码到HDU失败！CURL耗时：{$this->ch->getLastExecTime()}MS");
            $data = array(
                'id'            => $this->solutionId,
                'result'        => StatusVars::TIME_OUT,
                'time_cost'     => 0,
                'memory_cost'   => 0,
            );
            OjSolutionInterface::save($data);
            Logger::info('judge', "SOLUTION_ID：{$this->solutionId}，提交到HDU失败，写入TIME_OUT");
            return;
        } else {
            Logger::info('judge', "SOLUTION_ID：{$this->solutionId}，CURL成功提交代码到HDU！CURL耗时：{$this->ch->getLastExecTime()}MS");
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
            Logger::info('judge', "SOLUTION_ID：{$this->solutionId}，HDU成功获取RUN_ID！");
            if (in_array($rowInfo['result'], StatusVars::$hduResultMap)) {
                Logger::info('judge', "SOLUTION_ID：{$this->solutionId}，HDU远程judge成功！");
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

        if (!in_array($rowInfo['result'], StatusVars::$hduResultMap)) {
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
        if ($solutionInfo['remote'] != StatusVars::REMOTE_HDU) {
            Logger::error('judge', "SOLUTION_ID：{$this->solutionId}，必须是HDU的Solution！");
            throw new Exception("SOLUTION_ID：{$this->solutionId}，必须是HDU的Solution！");
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
            if (false === $rowInfo || in_array($rowInfo['result'], StatusVars::$hduResultMap)) {
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
            Logger::info('judge', "SOLUTION_ID：{$this->solutionId}，HDU远程judge成功！尝试次数：{$i}");

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

        $url = StatusVars::HDU_STATUS_URL . $this->username;
        if (!empty($runId)) {
            $url = $url . '&first=' . $runId;
        }
        $html = $this->ch->get($url);
        if (empty($html) || $this->ch->error()) {
            Logger::error('judge', "SOLUTION_ID：{$this->solutionId}，CURL 获取Status页面失败，RUNID：{$runId}，URL：{$url}");
            return false;
        }

        $html = iconv('GBK', 'UTF-8', $html);

        // 获取最新的一行
        $matches = array();
        $pattern = '/form action="\/status.php".*<tr[^>]*>(.*)<\/tr>/sU';
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
        $result = trim(preg_replace('/<[^>]*>/', '', $rowInfo[2]));

        // runtime error特殊处理
        $rtError = '';
        if (preg_match('/\((.*)\)/sU', $result, $matches)) {
            $result = 'Runtime Error';
            $rtError = $matches[1];
        }

        // 得到状态信息并且格式化
        $solutionInfo = array();
        if (!array_key_exists($result, StatusVars::$hduResultMap)) {
            $solutionInfo['result'] = StatusVars::RUNNING;
        } else {
            $solutionInfo['result'] = StatusVars::$hduResultMap[$result];
        }
        $solutionInfo['result_format']  = $result;
        $solutionInfo['run_id']         = $rowInfo[0];
        $solutionInfo['time_cost']      = (int) $rowInfo[4];
        $solutionInfo['memory_cost']    = (int) $rowInfo[5];

        // 获取调试信息
        $solutionInfo['judge_log'] = array();
        if ($solutionInfo['result'] == StatusVars::RUNTIME_ERROR) {
            $solutionInfo['judge_log']['re'] = $rtError;
        } else if ($solutionInfo['result'] == StatusVars::COMPILATION_ERROR) {
            $ce = $this->getCompileError($solutionInfo['run_id']);
            $solutionInfo['judge_log']['ce'] = (empty($ce) ? '' : $ce);
        }

        return $solutionInfo;
    }

    private function getCompileError($runId) {

        $url = StatusVars::HDU_COMPILE_INFO_URL . $runId;
        $html = $this->ch->get($url);
        if (false === $html) {
            Logger::error('judge', "SOLUTION_ID：{$this->solutionId}，获取Compile页面失败，CURL耗时：{$this->ch->getLastExecTime()}，RUNID：{$runId}，URL：{$url}");
            return false;
        }

        $html = iconv('GBK', 'UTF-8', $html);

        // 得到结果
        $matches = array();
        $pattern = '/View Compilation Error.*<pre>(.*)<\/pre>/sU';
        if (!preg_match($pattern, $html, $matches)) {
            Logger::error('judge', "SOLUTION_ID：{$this->solutionId}，正则匹配Compiation失败，RUNID：{$runId}，URL：{$url}");
            return false;
        }
        return $matches[1];
    }
}
