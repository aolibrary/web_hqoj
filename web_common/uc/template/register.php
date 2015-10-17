<!DOCTYPE html>
<html>
<head>
    <title>注册</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link type="text/css" href="http://sta.hqoj.net/css/common/init.css" rel="stylesheet">
    <style>
        .register { margin: 50px auto; background: #fff; width: 600px; padding: 20px 50px; border: 1px solid #ddd; box-shadow: 0 1px 5px 0 rgba(0,0,0,.3); -moz-box-shadow: 0 1px 5px 0 rgba(0,0,0,.3); -webkit-box-shadow: 0 1px 5px 0 rgba(0,0,0,.3); }
        .register h2 { padding-left: 130px; font-weight: normal; padding-bottom: 10px; margin-bottom: 20px; font-size: 20px; border-bottom: 2px dotted #bbb; }
        .register .submit-button { border-radius:4px; width: 190px; height: 32px; font-size: 16px; color: #fff; background-color: #eb4141; border: 0; }
        .register .submit-button:hover { background-color: #eb5050; }
    </style>
</head>
<body>
<form id="form" class="register widget-form">
    <h2>注册&nbsp;<span class="red">HQOJ</span></h2>
    <div class="item">
        <label class="w120 label">用户名</label>
        <input class="w180 input" type="text" name="username" data-validation="server" data-validation-url="/register/ajaxCheckUser/" />
    </div>
    <div class="item">
        <label class="ml130 green">4-20个字符，字母数字下划线组成，字母下划线开头！</label>
    </div>
    <div class="item">
        <label class="w120 label">密码</label>
        <input class="w180 input" type="password" name="password_confirmation" data-validation="length" data-validation-length="6-20" />
    </div>
    <div class="item">
        <label class="w120 label">确认密码</label>
        <input class="w180 input" type="password" name="password" data-validation="confirmation" data-validation-error-msg="两次输入密码不一致" />
    </div>
    <div class="item">
        <label class="w120 label">验证码</label>
        <input class="w90 input" type="text" name="verify" />
        <img name="img-code" src="/verify.php" height="30px" width="82px" />
    </div>
    <div class="item">
        <input type="submit" class="ml130 submit-button" value="立即注册" />
    </div>
    <input type="hidden" name="back-url" value="<?php echo Request::getGET('back-url', '//www.hqoj.net/'); ?>" />
</form>

<script src="http://sta.hqoj.net/js/cgi/sea.js"></script>
<script>
    seajs.use(['jquery', 'jquery.form-validator', 'js/util/jquery/plugin/form-validator/security.js', 'notice'], function($, fn1, fn2, notice) {

        fn1($);
        fn2($);

        $('img[name=img-code]').click(function() {
            $(this).attr('src', '/verify.php?v=' + Math.random());
        });

        $.validate({
            form : '#form',
            onSuccess : function() {

                var username = $('input[name=username]').val();
                var password = $('input[name=password]').val();
                var verify   = $('input[name=verify]').val();
                $.ajax({
                    url: '/register/ajaxSubmit/',
                    type: 'post',
                    dataType: 'json',
                    data: {
                        'username': username,
                        'password': password,
                        'verify': verify
                    },
                    success: function(result) {
                        if (0 === result.errorCode) {
                            notice('success', '注册成功，即将跳转到登录页面！', 1, function() {
                                location.href = '/login/?back-url=' + $('input[name=back-url]').val();
                            });
                        } else {
                            notice('error', result.errorMessage);
                            $('img[name=img-code]').attr('src', '/verify.php?v=' + Math.random());
                        }
                    },
                    error: function() {
                        notice('error', '服务器请求失败！');
                        $('img[name=img-code]').attr('src', '/verify.php?v=' + Math.random());
                    }
                });

                return false;
            }
        });
    });

</script>

</body>
</html>