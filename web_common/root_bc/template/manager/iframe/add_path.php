<form class="widget-form mt10" style="border: 0;">
    <div class="item">
        <label class="w120 label">管理员：</label>
        <input disabled value="<?php echo $this->userInfo['username']; ?>" name="username" class="w220 input" type="text" />
    </div>
    <div class="item">
        <label class="w120 label">路径：</label>
        <input name="path" class="w220 input" type="text" />
    </div>
    <div class="item">
        <input name="add" class="ml130 btn btn-blue w110" type="submit" value="添加" />
        <input name="cancel" class="w110 btn" type="button" value="取消" />
    </div>
    <input type="hidden" name="manager-id" value="<?php echo Request::getGET('manager-id'); ?>" />
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
                url: '/manager_path/ajaxAdd/',
                type: 'post',
                dataType: 'json',
                data: {
                    'manager-id': $('input[name=manager-id]').val(),
                    'path' : $('input[name=path]').val()
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