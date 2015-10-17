<form class="widget-form mt10" style="border: 0;">
    <div class="item">
        <label class="w120 label">用户名：</label>
        <input name="username" class="w220 input" type="text" />
    </div>
    <div class="item">
        <input name="set-user" class="ml130 btn btn-blue w120" type="submit" value="更换出题人" />
        <input name="cancel" class="btn w100" type="submit" value="取消" />
    </div>
    <input type="hidden" name="global-id" value="<?php echo Request::getGET('global-id'); ?>" />
</form>

<script>
    seajs.use(['jquery', 'notice'], function($, notice) {

        var index = parent.layer.getFrameIndex(window.name);

        $('input[name=cancel]').click(function() {
            parent.layer.close(index);
        });

        $('input[name=set-user]').click(function(e) {
            e.preventDefault();
            $.ajax({
                url: '/problem_list/ajaxSetUser/',
                type: 'post',
                dataType: 'json',
                data: {
                    'username': $('input[name=username]').val(),
                    'global-id': $('input[name=global-id]').val()
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