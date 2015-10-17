<link rel="stylesheet" href="//sta.hqoj.net/js/plugin/kindeditor/themes/default/default.css" />

<form class="widget-form bg-white">
    <div class="item">
        <label class="label">标题：</label>
        <input name="title" class="w400 input" type="text" value="<?php echo $this->docInfo['title']; ?>" />
    </div>
    <div>
        <label class="label">内容：</label>
    </div>
    <div>
        <textarea name="content" rows="30" style="width: 930px;"><?php echo htmlspecialchars($this->docInfo['content']); ?></textarea>
    </div>
    <div class="mt10 mb10">
        <input name="edit" type="button" class="btn btn-blue w120" value="提交" />
    </div>
    <input name="doc-id" type="hidden" value="<?php echo $this->docInfo['id']; ?>" />
</form>

<script src="//sta.hqoj.net/js/plugin/kindeditor/kindeditor.js"></script>
<script src="//sta.hqoj.net/js/plugin/kindeditor/lang/zh_CN.js"></script>

<script>
    seajs.use(['jquery', 'notice'], function($, notice) {

        KindEditor.ready(function(K) {
            var editor = K.create('textarea[name=content]', {
                cssPath : 'http://sta.hqoj.net/js/plugin/kindeditor/plugins/code/prettify.css',
                uploadJson : '/ke_upload/ajax/',
                allowFileManager : false,
                afterBlur: function () { this.sync(); }
            });
        });

        $('input[name=edit]').click(function(e) {
            e.preventDefault();
            $.ajax({
                url: '/edit/ajaxSubmit/',
                type: 'post',
                dataType: 'json',
                data: {
                    'doc-id'    : $('input[name=doc-id]').val(),
                    'title'     : $('input[name=title]').val(),
                    'content'   : $('textarea[name=content]').val()
                },
                success: function(result) {
                    if (0 === result.errorCode) {
                        location.href = '/detail/?doc-id=' + $('input[name=doc-id]').val();
                    } else {
                        notice('error', result.errorMessage);
                    }
                },
                error: function() {
                    notice('error', '服务器请求失败！');
                }
            });
        });
    });
</script>