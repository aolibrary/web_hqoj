<link rel="stylesheet" href="//sta.hqoj.net/js/plugin/kindeditor/themes/default/default.css" />

<form class="widget-form bg-white">
    <div class="item">
        <label class="label">题号：</label>
        <label class="label"><?php echo $this->problemInfo['problem_code']; ?></label>
    </div>
    <div class="item">
        <label class="label">标题：</label>
        <input name="title" class="w400 input" type="text" value="<?php echo $this->problemInfo['title']; ?>" />&nbsp;(50字以内)
    </div>
    <div class="item">
        <label class="label">分类：</label>
        <input name="source" class="w400 input" type="text" value="<?php echo $this->problemInfo['source']; ?>" />&nbsp;(50字以内，多个分类用<span class="red fw">英文逗号</span>分割)
    </div>
    <div class="item">
        <label class="label">限制：</label>
        <select class="select w120" name="time-limit">
            <?php foreach (StatusVars::$TIME_LIMIT as $value) {
                    $select = $value == $this->problemInfo['time_limit']/1000 ? 'selected' : '';
            ?>
                <option <?php echo $select; ?> value="<?php echo $value; ?>"><?php echo $value; ?>秒</option>
            <?php } ?>
        </select>
        <select class="select w120" name="memory-limit">
            <?php foreach (StatusVars::$MEMORY_LIMIT as $value) {
                    $select = $value == $this->problemInfo['memory_limit']/1024 ? 'selected' : '';
            ?>
                <option <?php echo $select; ?> value="<?php echo $value; ?>"><?php echo $value; ?>MB</option>
            <?php } ?>
        </select>
    </div>
    <div>
        <label class="label f14 fw">描述：</label>
    </div>
    <div>
        <textarea class="textarea-ke" name="description" rows="10" style="width: 1000px;"><?php echo htmlspecialchars($this->problemInfo['description']); ?></textarea>
    </div>
    <div>
        <label class="label f14 fw">输入描述：</label>
    </div>
    <div>
        <textarea class="textarea-ke" name="input" rows="5" style="width: 1000px;"><?php echo htmlspecialchars($this->problemInfo['input']); ?></textarea>
    </div>
    <div>
        <label class="label f14 fw">输出描述：</label>
    </div>
    <div>
        <textarea class="textarea-ke" name="output" rows="5" style="width: 1000px;"><?php echo htmlspecialchars($this->problemInfo['output']); ?></textarea>
    </div>
    <div>
        <label class="label f14 fw">输入例子：</label>
    </div>
    <div>
        <textarea class="textarea" name="sample-input" style="padding: 10px; font-family:Courier New,Courier,monospace; font-size: 12px; width: 950px; height: 250px; resize: none;"><?php echo htmlspecialchars($this->problemInfo['sample_input']); ?></textarea>
    </div>
    <div>
        <label class="label f14 fw">输出例子：</label>
    </div>
    <div>
        <textarea class="textarea" name="sample-output" style="padding: 10px; font-family:Courier New,Courier,monospace; font-size: 12px; width: 950px; height: 250px; resize: none;"><?php echo htmlspecialchars($this->problemInfo['sample_output']); ?></textarea>
    </div>
    <div>
        <label class="label f14 fw">提示：</label>
    </div>
    <div>
        <textarea class="textarea-ke" name="hint" rows="5" style="width: 1000px;"><?php echo htmlspecialchars($this->problemInfo['hint']); ?></textarea>
    </div>
    <div class="mt10 mb10">
        <input name="edit" type="button" class="btn btn-blue w120" value="修改" />
    </div>
    <input name="global-id" type="hidden" value="<?php echo $this->problemInfo['id']; ?>" />
</form>

<script src="//sta.hqoj.net/js/plugin/kindeditor/kindeditor.js"></script>
<script src="//sta.hqoj.net/js/plugin/kindeditor/lang/zh_CN.js"></script>

<script>
    seajs.use(['jquery', 'notice'], function($, notice) {
        
        KindEditor.ready(function(K) {
            var editor = K.create('textarea[class=textarea-ke]', {
                cssPath : 'http://sta.hqoj.net/js/plugin/kindeditor/plugins/code/prettify.css',
                uploadJson : '/setup_ke_upload/ajax/',
                autoHeightMode : true,
                afterCreate : function() {
                    this.loadPlugin('autoheight');
                },
                afterBlur: function () { this.sync(); },
                items : [
                        'source', '|', 'undo', 'redo', '|', 'preview', 'print', 'template', 'code', 'cut', 'copy', 'paste',
                        'plainpaste', 'wordpaste', '|', 'justifyleft', 'justifycenter', 'justifyright',
                        'justifyfull', 'insertorderedlist', 'insertunorderedlist', 'indent', 'outdent', 'subscript',
                        'superscript', 'clearhtml', 'quickformat', 'selectall', '|', 'fullscreen', '/',
                        'formatblock', 'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold',
                        'italic', 'underline', 'strikethrough', 'lineheight', 'removeformat', '|', 'image',
                        'table', 'hr', 'emoticons', 'pagebreak',
                        'anchor', 'link', 'unlink'
                ]
            });
        });
        
        $('input[name=edit]').click(function(e) {
            e.preventDefault();
            $.ajax({
                url: '/setup_problem_edit/ajaxSubmit/',
                type: 'post',
                dataType: 'json',
                data: {
                    'global-id'     : $('input[name=global-id]').val(),
                    'title'         : $('input[name=title]').val(),
                    'source'        : $('input[name=source]').val(),
                    'time-limit'    : $('select[name=time-limit]').val(),
                    'memory-limit'  : $('select[name=memory-limit]').val(),
                    'description'   : $('textarea[name=description]').val(),
                    'input'         : $('textarea[name=input]').val(),
                    'output'        : $('textarea[name=output]').val(),
                    'sample-input'  : $('textarea[name=sample-input]').val(),
                    'sample-output' : $('textarea[name=sample-output]').val(),
                    'hint'          : $('textarea[name=hint]').val()
                },
                success: function(result) {
                    if (0 === result.errorCode) {
                        location.href = '/setup_problem_detail/?global-id=' + $('input[name=global-id]').val();
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