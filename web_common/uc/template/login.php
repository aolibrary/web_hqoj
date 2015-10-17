<!DOCTYPE html>
<html>
<head>
    <title>登录</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link type="text/css" href="//sta.hqoj.net/css/common/init.css" rel="stylesheet">
    <style>
        body { background: #f8f8f8 url(http://sta.hqoj.net/image/uc/bg.png) left top repeat; }
        .module-header { width: 100%; min-width: 1200px; height: 70px; background: #f8f8f8 url(http://sta.hqoj.net/image/uc/bg.png) left top repeat; border-top: 3px solid #eb4141; }
        .module-header2 { margin: 0 auto; width: 1200px; height: 70px; }
        .module-header .logo { display: inline-block; margin-left: 20px;}
        .module-header .logo a { display: inline-block; height: 70px; line-height: 70px; font-size: 30px; font-family: cursive; color: #333; }
        .module-header .logo span { height: 70px; line-height: 70px; font-size: 30px; font-family: cursive; }
        .module-wrap { position: relative; margin: 0 auto; width: 1200px; height: 500px; }
        .module-wrap .login { position: absolute; top: 50px; right: 80px; padding: 10px 30px 20px 30px; width: 294px; background-color: #fff; border: 1px solid #ddd; box-shadow: 0 1px 5px 0 rgba(0,0,0,.3); -moz-box-shadow: 0 1px 5px 0 rgba(0,0,0,.3); -webkit-box-shadow: 0 1px 5px 0 rgba(0,0,0,.3); }
        .module-wrap .login .title { padding: 10px; font-weight: normal; font-size: 20px; border-bottom: 2px dotted #bbb; }
        .module-wrap .login input { margin-top: 10px; padding: 4px 6px; width: 280px; height: 24px; line-height: 24px; font-size: 16px; display: block; border: 1px solid #ddd; }
        .module-wrap .login .login-btn { cursor: pointer; margin: 10px 0; width: 294px; height: 35px; padding: 6px 10px; font-size: 16px; color: #fff; background-color: #eb4141; border: 0; }
        .module-wrap .login .login-btn:hover { background-color: #eb5050; }
    </style>
</head>
<body>
<div class="module-header">
    <div class="module-header2">
        <div class="logo"><a href="//www.hqoj.net/">HQu Online Judge</a>&nbsp;<span class="red">.</span></div>
    </div>
</div>

<div class="module-wrap">
    <form class="login">
        <div class="title">登录&nbsp;<span class="red">HQOJ</span></div>
        <input type="text" name="login-name" placeholder="用户名 | 邮箱 | 手机" autocomplete="off" />
        <input type="password" name="password" placeholder="密码" autocomplete="off" />
        <a class="fr mt5" href="/find/">忘记密码？</a>
        <button class="login-btn" id="login-submit">登录</button>
        <div id="message-box" class="red"></div>
        <input name="back-url" type="hidden" value="<?php echo Request::getGET('back-url', '//www.hqoj.net/'); ?>" />
    </form>
</div>

<script src="http://sta.hqoj.net/js/cgi/sea.js"></script>
<script>
    seajs.use(['jquery', 'js/util/placeholder/PlaceHolder.js', 'js/util/crypt/md5.js', 'js/util/crypt/sha1.js'], function($, oPlaceHolder, md5, sha1) {

        oPlaceHolder.init();

        $('#login-submit').click(function(e) {
            e.preventDefault();
            $('#message-box').html('');
            var password = $('input[name=password]').val();
            password = sha1(md5(password));
            $.ajax({
                url: '/login/ajaxLogin/',
                type: 'post',
                dataType: 'json',
                data: {
                    'login-name': $('input[name=login-name]').val(),
                    'password': password,
                    'back-url': $('input[name=back-url]').val()
                },
                success: function(result) {
                    if (0 === result.errorCode) {
                        location.href = $('input[name=back-url]').val();
                    } else {
                        $('#message-box').html(result.errorMessage);
                    }
                },
                error: function() {
                    $('#message-box').html('服务器请求失败！');
                }
            });
        });

    });
</script>

</body>
</html>