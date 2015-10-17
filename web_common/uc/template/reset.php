<!DOCTYPE html>
<html>
<head>
    <title>重置密码</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link type="text/css" href="http://sta.hqoj.net/css/common/init.css" rel="stylesheet">
    <style>
        .reset-form { margin: 50px auto; background: #fff; width: 600px; padding: 20px 50px; border: 1px solid #ddd; box-shadow: 0 1px 5px 0 rgba(0,0,0,.3); -moz-box-shadow: 0 1px 5px 0 rgba(0,0,0,.3); -webkit-box-shadow: 0 1px 5px 0 rgba(0,0,0,.3); }
        .reset-form h2 { padding-left: 130px; font-weight: normal; padding-bottom: 10px; margin-bottom: 20px; font-size: 20px; border-bottom: 2px dotted #bbb; }
        .reset-form .submit-button { border-radius:4px; width: 190px; height: 32px; font-size: 16px; color: #fff; background-color: #eb4141; border: 0; }
        .reset-form .submit-button:hover { background-color: #eb5050; }
    </style>
</head>
<body>
<form id="form" class="reset-form widget-form">
    <h2><span class="red">重置密码</span></h2>
    <div class="item">
        <label class="w120 label">用户名：</label>
        <label name="username" class="label ml5"><?php echo $this->userInfo['username']; ?></label>
    </div>
    <div class="item">
        <label class="w120 label">新密码：</label>
        <input class="w180 input" type="password" name="password_confirmation" data-validation="length" data-validation-length="6-30" />
    </div>
    <div class="item">
        <label class="w120 label">确认密码：</label>
        <input class="w180 input" type="password" name="password" data-validation="confirmation" data-validation-error-msg="两次输入密码不一致" />
    </div>
    <div class="item">
        <input type="submit" class="ml130 submit-button" value="完成修改" />
    </div>
    <input type="hidden" name="reset-ticket" value="<?php echo Request::getGET('reset-ticket', ''); ?>" />
</form>

<script src="http://sta.hqoj.net/js/cgi/sea.js"></script>
<script>
    seajs.use(['jquery', 'jquery.form-validator', 'js/util/jquery/plugin/form-validator/security.js', 'notice'], function($, fn1, fn2, notice) {

        fn1($);
        fn2($);

        $.validate({
            form : '#form',
            onSuccess : function() {

                var resetTicket = $('input[name=reset-ticket]').val();
                var password    = $('input[name=password]').val();
                $.ajax({
                    url: '/reset/ajaxSubmit/',
                    type: 'post',
                    dataType: 'json',
                    data: {
                        'reset-ticket' : resetTicket,
                        'password': password
                    },
                    success: function(result) {
                        if (0 === result.errorCode) {
                            notice('success', '您已成功修改密码，即将跳转到登录页面！', 1, function() {
                                location.href = '/login/';
                            });
                        } else {
                            notice('error', result.errorMessage);
                        }
                    },
                    error: function() {
                        notice('error', '服务器请求失败！');
                    }
                });

                return false;
            }
        });
    });
</script>
</body>
</html>