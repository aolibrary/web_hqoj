<?php

class UcAuthLogic {

    public static function sendEmailCode($email, $repeatAt) {

        $memcached = MemcachedPool::getMemcached(MemcachedConfig::$SERVER_COMMON);
        $key = MemcachedKeys::UC_EMAIL_AUTH_ . $email;
        $info = $memcached->get($key);

        // 如果没有过期
        if (!empty($info)) {
            // 如果刷新时间没到，那么直接返回false
            if ($info['repeat_at'] > time()) {
                return false;
            }
            $code = $info['code'];  // 重复发送相同的验证码
        } else {
            $code = self::makeCode();
        }

        // 发送邮件
        $subject = 'HQOJ验证码';
        $body    = "您的验证码为：{$code}";
        $emailClient = new EmailClient(EmailConfig::$SENDER_ADMIN);
        $emailClient->sendAsync($subject, $body, $email);

        $info = array(
            'code'      => $code,
            'repeat_at' => $repeatAt,
        );
        $memcached->set($key, $info, 600);  // 设置验证码有效时间为10分钟
        return $code;
    }

    private static function makeCode() {
        $ip = Http::getClientIp();
        $ip = $ip%1000000;
        $code = rand(100000, 999999);
        $code = ($code+$ip)%1000000;
        return sprintf('%06d', $code);
    }

    public static function checkEmailCode($email, $code) {

        $memcached = MemcachedPool::getMemcached(MemcachedConfig::$SERVER_COMMON);
        $key = MemcachedKeys::UC_EMAIL_AUTH_ . $email;
        $info = $memcached->get($key);
        if (false === $info) {
            return false;
        }
        return ($info['code'] == $code ? true : false);
    }

    public static function deleteEmailCode($email) {

        $memcached = MemcachedPool::getMemcached(MemcachedConfig::$SERVER_COMMON);
        $key = MemcachedKeys::UC_EMAIL_AUTH_ . $email;
        return $memcached->delete($key);
    }

    public static function makeResetTicket($loginName) {

        $memcached = MemcachedPool::getMemcached(MemcachedConfig::$SERVER_COMMON);

        // 生成唯一的md5，最多执行3次
        $i = 0;
        do {
            $resetTicket = md5(uniqid($loginName, true));
            $key = MemcachedKeys::UC_RESET_TICKET_ . $resetTicket;
            $ret = $memcached->get($key);
            if (false === $ret) {
                $memcached->set($key, $loginName, 1800);   // resetTicket半小时有效
                break;
            }
            $i ++;
        } while (true);

        // 发生碰撞
        if ($i >= 3) {
            Logger::fatal('interface.error.uc', "设置resetTicket次数为{$i}！");
        }

        return $resetTicket;
    }

    public static function getUserInfoByResetTicket($resetTicket) {

        $memcached = MemcachedPool::getMemcached(MemcachedConfig::$SERVER_COMMON);
        $key = MemcachedKeys::UC_RESET_TICKET_ . $resetTicket;
        $loginName = $memcached->get($key);
        if (false === $loginName) {
            return array();
        }
        $userInfo = UcUserInterface::getByLoginName(array('login_name' => $loginName));
        return $userInfo;
    }

    public static function deleteResetTicket($resetTicket) {

        $memcached = MemcachedPool::getMemcached(MemcachedConfig::$SERVER_COMMON);
        $key = MemcachedKeys::UC_RESET_TICKET_ . $resetTicket;
        return $memcached->delete($key);
    }

}
