
<form id="permission-add-form" class="widget-form mt10" style="border: 0;">
    <div class="item">
        <label class="w120 label">权限码：</label>
        <input disabled value="<?php echo $this->permissionInfo['code']; ?>" name="code" class="w220 input" type="text"  />
    </div>
    <div class="item">
        <label class="w120 label">描述：</label>
        <input value="<?php echo $this->permissionInfo['description']; ?>" name="description" class="w220 input" type="text" data-validation="required length"  data-validation-length="1-100" />
    </div>
    <div class="item">
        <input id="permission-add-btn" class="ml130 w110 btn btn-blue" type="submit" value="修改" />
        <input name="cancel" class="w110 btn" type="button" value="取消" />
    </div>
    <input name="permission-id" type="hidden" value="<?php echo $this->permissionInfo['id']; ?>" />
</form>

<script>
    seajs.use(['jquery', 'jquery.form-validator', 'notice'], function($, fn1, notice) {

        fn1($);

        var index = parent.layer.getFrameIndex(window.name);

        $('input[name=cancel]').click(function() {
            parent.layer.close(index);
        });

        $.validate({
            'form' : '#permission-add-form',
            'onSuccess' : function() {
                $.ajax({
                    url: '/permission_edit/ajaxSubmit/',
                    type: 'post',
                    dataType: 'json',
                    data: {
                        'permission-id': $('input[name=permission-id]').val(),
                        'description': $('input[name=description]').val()
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
                return false;
            }
        });
    });
</script>