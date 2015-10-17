<?php

require_once __DIR__ . '/../util/PHPMailer-master/PHPMailerAutoload.php';

class EmailClient {

    public $phpMail    = null;
    public $config     = array();

    public function __construct($config) {

        $this->config = $config;

        // 配置
        $this->phpMail = new PHPMailer;
        $this->phpMail->CharSet = 'UTF-8';
        $this->phpMail->isSMTP();
        $this->phpMail->SMTPAuth    = true;
        $this->phpMail->Host        = $this->config['host'];
        $this->phpMail->SMTPSecure  = $this->config['secure'];
        $this->phpMail->Port        = $this->config['port'];
        $this->phpMail->Username    = $this->config['username'];
        $this->phpMail->Password    = $this->config['password'];
        $this->phpMail->From        = $this->config['from'];
        $this->phpMail->FromName    = $this->config['from_name'];

        $this->phpMail->WordWrap    = 100;
        $this->phpMail->isHTML(true);

    }

    /**
     * 发送邮件
     *
     * @param   $subject    string 邮件主题
     * @param   $body       string 邮件正文
     * @param   $to         string | array 对方
     * @param   $cc         string | array 抄送
     * @param   $bcc        string | array 秘密抄送
     * @return  true | false
     */
    public function send($subject, $body, $to = '', $cc = '', $bcc = '') {

        // 如果是异步

        // 清空原先的接收方
        $this->phpMail->clearAddresses();
        $this->phpMail->clearCCs();
        $this->phpMail->clearBCCs();

        $to = (array) $to;
        foreach ($to as $v) {
            $this->phpMail->addAddress($v);
        }

        $cc = (array) $cc;
        foreach ($cc as $v) {
            $this->phpMail->addCC($v);
        }

        $bcc = (array) $bcc;
        foreach ($bcc as $v) {
            $this->phpMail->addBCC($v);
        }

        $this->phpMail->Subject = $subject;
        $this->phpMail->Body    = $body;

        $ret = $this->phpMail->send();
        return $ret;
    }

    /**
     * 发送邮件
     *
     * @param   $subject    string 邮件主题
     * @param   $body       string 邮件正文
     * @param   $to         string | array 对方
     * @param   $cc         string | array 抄送
     * @param   $bcc        string | array 秘密抄送
     * @return  true | false
     */
    public function sendAsync($subject, $body, $to = '', $cc = '', $bcc = '') {

        $params = array(
            'config'    => $this->config,
            'subject'   => $subject,
            'body'      => $body,
            'to'        => $to,
            'cc'        => $cc,
            'bcc'       => $bcc,
        );

        $gearman = GearmanPool::getClient(GearmanConfig::$SERVER_COMMON);
        $gearman->doBackground('email_async', json_encode($params));
        return true;
    }
}
