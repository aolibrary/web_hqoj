<form class="widget-form mt10" style="border: 0;">
    <div class="item">
        <label class="w120 label">登录名：</label>
        <input name="login-name" class="w220 input" type="text" placeholder="用户名 | 邮箱 | 手机" />
    </div>
    <div class="item">
        <input name="add" class="ml130 btn btn-blue w120" type="submit" value="添加到管理员" />
        <input name="cancel" class="btn w100" type="submit" value="取消" />
    </div>
</form>

<script>
    seajs.use(['jquery', 'notice'], function($, notice) {

        var index = parent.layer.getFrameIndex(window.name);

        $('input[name=cancel]').click(function() {
            parent.layer.close(index);
        });

        $('input[name=add]').click(function(e) {
            e.preventDefault();
            $.ajax({
                url: '/manager_add/ajaxAdd/',
                type: 'post',
                dataType: 'json',
                data: {
                    'login-name': $('input[name=login-name]').val()
                },
                success: function(result) {
                    if (0 === result.errorCode) {
                        parent.location.reload();
                    } else {
                        notice('error', result.errorMessage);
                    }
                },
                error: function() {
                    notice('error', '服务器请求失败！');
                }
            });
        });
    })
</script>