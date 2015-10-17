<form id="form" class="widget-form">
    <div class="item">
        <label class="label w120">旧密码：</label>
        <input name="old-password" class="input w180" type="password" data-validation="length" data-validation-length="6-30" data-validation-error-msg="请正确输入原密码" />
    </div>
    <div class="item">
        <label class="label w120">新密码：</label>
        <input class="input w180" type="password" name="password_confirmation" data-validation="length" data-validation-length="6-30" />
    </div>
    <div class="item">
        <label class="label w120">确认密码：</label>
        <input class="input w180" type="password" name="password" data-validation="confirmation" data-validation-error-msg="两次输入密码不一致" />
    </div>
    <div class="item">
        <input class="btn btn-blue w180 ml130" type="submit" value="修改" />
    </div>
</form>

<script>
    seajs.use(['jquery', 'jquery.form-validator', 'js/util/jquery/plugin/form-validator/security.js', 'notice'], function($, fn1, fn2, notice) {

        fn1($);
        fn2($);

        $.validate({
            form : '#form',
            onSuccess : function() {

                $.ajax({
                    url: '/setup_uc_password/ajaxUpdatePassword/',
                    type: 'post',
                    dataType: 'json',
                    data: {
                        'old-password': $('input[name=old-password]').val(),
                        'password': $('input[name=password]').val()
                    },
                    success: function(result) {
                        if (0 === result.errorCode) {
                            notice('success', '修改密码成功，请重新登录！', 1, function() {
                                location.reload();
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