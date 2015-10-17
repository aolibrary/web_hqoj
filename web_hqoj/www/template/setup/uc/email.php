<style>
#form h2 { padding-left: 130px; font-weight: normal; padding-top: 10px; margin: 15px 0 5px; font-size: 14px; border-top: 1px dotted #bbb; }
</style>

<form id="form" class="widget-form">
    <div class="item">
        <label class="w120 label">当前邮箱：</label>
        <label class="label"><?php echo Arr::get('email', $this->loginUserInfo, '未绑定', true); ?></label>
    </div>
    <h2><span>绑定新邮箱</span></h2>
    <div class="item">
        <label class="w120 label">绑定的邮箱：</label>
        <input class="w180 input" type="text" name="email" />
        <input name="send-email" type="button" class="btn w100" value="发送验证码" />
    </div>
    <div class="item">
        <label class="w120 label">邮箱验证码：</label>
        <input class="w180 input" type="text" name="check-code" />
    </div>
    <div class="item">
        <input class="btn btn-blue w180 ml130" type="submit" value="绑定" />
    </div>
</form>

<script>
    seajs.use(['jquery', 'jquery.form-validator', 'js/util/jquery/plugin/form-validator/security.js', 'notice'], function($, fn1, fn2, notice) {
        
        fn1($);
        fn2($);
        
        $('input[name=send-email]').click(function(e) {
            e.preventDefault();
            var $this = $(this);
            if ($this.hasClass('btn-forbidden')) {
                return false;
            }
            $this.addClass('btn-forbidden');
            $this.val('发送中...');
            $.ajax({
                url: '/setup_uc_email/ajaxSendEmail/',
                type: 'post',
                dataType: 'json',
                data: {
                    'email': $('input[name=email]').val()
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
                    } else {
                        notice('error', result.errorMessage);
                        $this.val('发送验证码');
                        $this.removeClass('btn-forbidden');
                    }
                },
                error: function() {
                    notice('error', '服务器请求失败！');
                    $this.val('发送验证码');
                    $this.removeClass('btn-forbidden');
                }
            });
        });
        
        $.validate({
            form : '#form',
            onSuccess : function() {
                
                var email     = $('input[name=email]').val();
                var checkCode = $('input[name=check-code]').val();
                $.ajax({
                    url: '/setup_uc_email/ajaxSubmit/',
                    type: 'post',
                    dataType: 'json',
                    data: {
                        'email': email,
                        'check-code': checkCode
                    },
                    success: function(result) {
                        if (0 === result.errorCode) {
                            location.reload();
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