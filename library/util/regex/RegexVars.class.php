<?php

class RegexVars {

    // 用户名，4-20位，字母数字下划线，字母下划线开头
    const USERNAME = '/^[A-Za-z_][A-Za-z0-9_]{3,19}$/';

    // 密码，6-20位字符
    const PASSWORD = '/^.{6,20}$/';

    // 数字
    const NUMBER = '/^[+-]?[1-9][0-9]*(\.[0-9]+)?([eE][+-][1-9][0-9]*)?$|^[+-]?0?\.[0-9]+([eE][+-][1-9][0-9]*)?$/';

    // 整数
    const INT = '/^-?[0-9]\d*$/';

    //正整数
    const UP_INT = '/^[1-9]\d*$/';

    //非负整数
    const HIGH_INT = '/^\d+$/';

    //非负数，包括（小数，0，正整数)
    const HIGH_FLOAT_INT_ZERO = '/^([1-9]\d*|\d+\.\d+|0)$/';

    //非负数，包括（小数，正整数)
    const HIGH_FLOAT_INT = '/^([1-9]\d*|\d+\.\d+)$/';

    // 邮箱
    const EMAIL = '/^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/';

    // 手机
    const MOBILE = '/^1[34587]\d{9}$/';

    // 电话
    const TELEPHONE = '/^(?:(?:0\d{2,3}[- ]?[1-9]\d{6,7})|(?:[48]00[- ]?[1-9]\d{6}))$/';

    // 手机 + 电话
    const PHONE = '/^(1[34587]\d{9}|(?:(?:0\d{2,3}[- ]?[1-9]\d{6,7}))|(?:[48]00[- ]?[1-9]\d{6}))$/';

    //不包含手机号码
    const NO_PHONE = '/1[34587]\d{9}|(0\d{2,4}-)?[2-9]\d{6,7}(-\d{2,5})?|(?!\d+(-\d+){3,})[48]00(-?\d){7,10}/';

    // 身份证
    const IDENTITY = '/(^\d{15}$)|(^\d{17}(?:\d|x|X)$)/';

    // 组织机构代码证
    const CERT_NUM = '/^[a-zA-Z0-9]{8}-[a-zA-Z0-9]$/';

    //身份证或组织机构代码证
    const IDENTITY_CERT_NUM = '/(^\d{15}$)|(^\d{17}(?:\d|x|X)$)|(^[a-zA-Z0-9]{8}-[a-zA-Z0-9]$)/';

    // url
    const URL = '/^(http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?$/';

    // 日期
    const DATE = '/^\d{4}(-|\/|\.)\d{1,2}\1\d{1,2}$/';

    // 邮编
    const CODE = '/^\d{6}$/';

    // qq (5到11位数字)
    const QQ = '/^[1-9][0-9]{4,10}$/';

    // js中文正则
    const CN = '/^[\u4E00-\u9FA5\uF900-\uFA2D]+$/';

    // php中文正则
    const PHP_CN = '/^[\x80-\xff]+$/';

    // ip_v4
    const IP4 = '/^(25[0-5]|2[0-4]\d|[0-1]\d{2}|[1-9]?\d)\.(25[0-5]|2[0-4]\d|[0-1]\d{2}|[1-9]?\d)\.(25[0-5]|2[0-4]\d|[0-1]\d{2}|[1-9]?\d)\.(25[0-5]|2[0-4]\d|[0-1]\d{2}|[1-9]?\d)$/';

    // 颜色
    const COLOR = '/^#?[a-fA-F0-9]{6}$/';

    // ASCII字符
    const ASCII = '/^[\x00-\xFF]+$/';

    // 压缩文件
    const ZIP = '/(.*)\.(rar|zip|7zip|tgz)$/i';

    // 图片
    const IMAGE = '/(.*)\.(jpg|bmp|gif|ico|pcx|jpeg|tif|png|raw|tga)$/i';

    // 特殊字符
    const NO_SPECIAL = '/※|◆|▌|▎|▏|▓|▔|▕|■|□|▲|△|▼|▽|◆|◇|○|☉|☆|★|◥|◤|◣|◢|●|◎|♀|♂|〓|㊣|℅|▇|▇██|▇▇▇██▇▇▇▇█|▇▇▇▇|██▇|██|▄▄|▄|▁|▃▂|█|▂/';

    //车牌号js
    const CAR_NUMBER = '/^[\u4e00-\u9fa5]{1}([A-Z0-9]{1}|[0-9]{2})(([0-9ABCDEFGHJKLMNPQRSTUVWXYZ]{5})|([0-9ABCDEFGHJKLMNPQRSTUVWXYZ]{4}(学|挂)))$/';

    //车牌号php
    const PHP_CAR_NUMBER = '/^[\x80-\xff]{3}([A-Z0-9]{1}|[0-9]{2})(([0-9ABCDEFGHJKLMNPQRSTUVWXYZ]{5})|([0-9ABCDEFGHJKLMNPQRSTUVWXYZ]{4}(学|挂)))$/';


}