<!DOCTYPE html>
<html>
<head>
    <title>找回密码</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link type="text/css" href="http://sta.hqoj.net/css/common/init.css" rel="stylesheet">
    <style>
        .find-form { margin: 50px auto; background: #fff; width: 600px; padding: 20px 50px; border: 1px solid #ddd; box-shadow: 0 1px 5px 0 rgba(0,0,0,.3); -moz-box-shadow: 0 1px 5px 0 rgba(0,0,0,.3); -webkit-box-shadow: 0 1px 5px 0 rgba(0,0,0,.3); }
        .find-form h2 { padding-left: 130px; font-weight: normal; padding-bottom: 10px; margin-bottom: 20px; font-size: 20px; border-bottom: 2px dotted #bbb; }
        .find-form .submit-button { border-radius:4px; width: 190px; height: 32px; font-size: 16px; color: #fff; background-color: #eb4141; border: 0; }
        .find-form .submit-button:hover { background-color: #eb5050; }
    </style>
</head>
<body>
<form id="form" class="find-form widget-form">
    <h2><span class="red">找回密码</span></h2>
    <div class="item">
        <label class="w120 label">图片验证码</label>
        <input class="w90 input" type="text" name="verify" />
        <img name="img-code" src="/verify.php" height="30px" width="82px" />
    </div>
    <div class="item">
        <label class="w120 label">绑定的邮箱</label>
        <input class="w180 input" type="text" name="email" />
        <input name="send-email" type="button" class="btn w100" value="发送验证码" />
    </div>
    <div class="item">
        <label class="w120 label">邮箱验证码</label>
        <input class="w180 input" type="text" name="check-code" />
    </div>
    <div class="item">
        <input type="submit" class="ml130 submit-button" value="下一步" />
    </div>
</form>

<script src="http://sta.hqoj.net/js/cgi/sea.js"></script>
<script>
    seajs.use(['jquery', 'jquery.form-validator', 'js/util/jquery/plugin/form-validator/security.js', 'notice'], function($, fn1, fn2, notice) {

        fn1($);
        fn2($);

        $('img[name=img-code]').click(function() {
            $(this).attr('src', '/verify.php?v=' + Math.random());
        });

        $('input[name=send-email]').click(function(e) {
            e.preventDefault();

            // 先填写图片验证码
            var verify   = $('input[name=verify]').val();
            if (!verify) {
                notice('error', '请先填写验证码！');
                return false;
            }

            var $this = $(this);
            if ($this.hasClass('btn-forbidden')) {
                return false;
            }
            $this.addClass('btn-forbidden');
            $this.val('发送中...');
            $.ajax({
                url: '/find/ajaxSendEmail/',
                type: 'post',
                dataType: 'json',
                data: {
                    'email': $('input[name=email]').val(),
                    'verify': verify
                },
                success: function(result) {
                    if (0 === result.errorCode) {
                        notice('success', '验证码已发送！');
                        var second = 60;
                        var d = setInterval(function() {
                            $this.val(second+'s');
                            if (second == 0) {
                                clearInterval(d);
                                $this.val('重新发送');
                                $this.removeClass('btn-forbidden');
                            }
                            second--;
                        }, 1000);
                    } else if (2 === result.errorCode) {
                        notice('error', result.errorMessage);
                        $this.val('发送验证码');
                        $this.removeClass('btn-forbidden');
                        $('img[name=img-code]').attr('src', '/verify.php?v=' + Math.random());
                    } else {
                        notice('error', result.errorMessage);
                        $this.val('发送验证码');
                        $this.removeClass('btn-forbidden');
                    }
                },
                error: function() {
                    notice('error', '服务器请求失败！');
                    $this.val('重新发送');
                    $this.removeClass('btn-forbidden');
                    $('img[name=img-code]').attr('src', '/verify.php?v=' + Math.random());
                }
            });
        });

        $.validate({
            form : '#form',
            onSuccess : function() {

                var verify    = $('input[name=verify]').val();
                var email     = $('input[name=email]').val();
                var checkCode = $('input[name=check-code]').val();
                $.ajax({
                    url: '/find/ajaxSubmit/',
                    type: 'post',
                    dataType: 'json',
                    data: {
                        'verify': verify,
                        'email': email,
                        'check-code': checkCode
                    },
                    success: function(result) {
                        if (0 === result.errorCode) {
                            location.href = '/reset/?reset-ticket=' + result.resetTicket;
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