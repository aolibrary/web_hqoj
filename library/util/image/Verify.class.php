<?php

class Verify {

    private $charset  = 'abcdefghkmnprstuvwxyzABCDEFGHKMNPRSTUVWXYZ23456789';    // 字符集
    private $code     = '';    //验证码
    private $length   = 4;     //验证码长度
    private $width    = 150;   //宽度
    private $height   = 50;    //高度
    private $img      = null;  //图形资源句柄
    private $family   = '';    //指定的字体
    private $fontSize = 30;    //指定字体大小

    public function __construct($width = 150, $height = 50, $fontSize = 30, $length = 4, $family = 'VeraSansBold.ttf') {

        $this->width    = $width;
        $this->height   = $height;
        $this->fontSize = $fontSize;
        $this->length   = $length;
        $this->family   = __DIR__ . '/fonts/' . $family;
        $this->createBg();
        $this->createCode();
        $this->createLine();
        $this->createFont();
    }

    // 生成验证码
    private function createCode() {

        $len = strlen($this->charset)-1;
        for ($i = 0; $i < $this->length; $i++) {
            $this->code .= $this->charset[mt_rand(0,$len)];
        }
    }

    // 生成背景
    private function createBg() {

        $this->img = imagecreatetruecolor($this->width, $this->height);
        $color = imagecolorallocate($this->img, mt_rand(157,255), mt_rand(157,255), mt_rand(157,255));
        imagefilledrectangle($this->img, 0, $this->height, $this->width, 0, $color);
    }

    // 生成文字
    private function createFont() {

        $_x = $this->width / $this->length;
        for ($i = 0; $i < $this->length; $i++) {
            $color = imagecolorallocate($this->img, mt_rand(0,156), mt_rand(0,156), mt_rand(0,156));
            imagettftext($this->img, $this->fontSize, mt_rand(-30,30), $_x*$i+mt_rand(3,5), $this->height/1.4, $color, $this->family, $this->code[$i]);
        }
    }

    // 生成线条、雪花
    private function createLine() {

        for ($i = 0; $i < 6; $i++) {
            $color = imagecolorallocate($this->img, mt_rand(0,156), mt_rand(0,156), mt_rand(0,156));
            imageline($this->img, mt_rand(0, $this->width), mt_rand(0, $this->height), mt_rand(0, $this->width), mt_rand(0, $this->height), $color);
        }
        for ($i = 0; $i < 100; $i++) {
            $color = imagecolorallocate($this->img, mt_rand(200,255), mt_rand(200,255), mt_rand(200,255));
            imagestring($this->img, mt_rand(1,5), mt_rand(0,$this->width), mt_rand(0,$this->height), '*', $color);
        }
    }

    // 输出
    public function output() {

        header('Content-type:image/png');
        imagepng($this->img);
        imagedestroy($this->img);
    }

    // 获取验证码
    public function getCode() {

        return strtolower($this->code);
    }
}