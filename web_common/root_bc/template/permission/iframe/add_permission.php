
<form id="permission-add-form" class="widget-form mt10" style="border: 0;">
    <div class="item">
        <label class="w120 label">权限码：</label>
        <input name="code" class="w220 input" type="text" data-validation="custom" data-validation-regexp="^\/[a-zA-z][a-zA-z0-9\/]*$"  />
    </div>
    <div class="item">
        <label class="w120 label">描述：</label>
        <input name="description" class="w220 input" type="text" data-validation="required length"  data-validation-length="1-100" />
    </div>
    <div class="item">
        <input id="add-btn" class="ml130 w110 btn btn-blue" type="submit" value="添加权限" />
        <input name="cancel" class="w110 btn" type="button" value="取消" />
    </div>
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
                var code = $('input[name=code]').val();
                $.ajax({
                    url: '/permission_add/ajaxSubmit/',
                    type: 'post',
                    dataType: 'json',
                    data: {
                        'code': code,
                        'description': $('input[name=description]').val()
                    },
                    success: function(result) {
                        if (0 === result.errorCode) {
                            parent.location.href = parent.location.pathname + '?search=' + code;
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