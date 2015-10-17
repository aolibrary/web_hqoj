<link rel="stylesheet" href="//sta.hqoj.net/js/plugin/kindeditor/themes/default/default.css" />

<form class="widget-form bg-white">
    <div class="item">
        <label class="label w80">ID：</label>
        <label class="label"><?php echo $this->contestInfo['id']; ?></label>
    </div>
    <div class="item">
        <label class="label w80">标题：</label>
        <input name="title" class="w400 input" type="text" value="<?php echo $this->contestInfo['title']; ?>" />&nbsp;(50字以内)
    </div>
    <div class="item">
        <label class="label w80">访问权限：</label>
        <?php foreach (ContestVars::$TYPE_FORMAT as $key => $value) {
            if (empty($key)) {
                continue;
            }
            $check = $key == $this->contestInfo['type'] ? 'checked' : '';
            ?>
            <label class="label"><input <?php echo $check; ?> type="radio" class="radio" name="type" value="<?php echo $key; ?>" />&nbsp;<?php echo $value; ?>&nbsp;</label>
        <?php } ?>
    </div>
    <div class="item">
        <label class="label w80">密码：</label>
        <input name="password" class="w180 input" type="text" value="<?php echo $this->contestInfo['password']; ?>" />&nbsp;(20字符以内，要求是字母数字和下划线组成，比赛是公开或者报名可以不填写)
    </div>
    <div class="item">
        <label class="label w80">提示：</label>
        <input name="notice" class="w400 input" type="text" value="<?php echo $this->contestInfo['notice']; ?>" />&nbsp;(100字以内，比赛中会以文字滚动形式展现，可以不填)
    </div>
    <div class="item">
        <label class="label w80">开始时间：</label>
        <input <?php echo $this->contestInfo['is_active'] ? 'disabled="true"' : ''; ?> name="begin-date" class="tc input w90" type="text" value="<?php echo $this->contestInfo['begin_time'] ? date('Y-m-d', $this->contestInfo['begin_time']) : date('Y-m-d', time()); ?>" />&nbsp;
        <select <?php echo $this->contestInfo['is_active'] ? 'disabled="true"' : ''; ?> class="select" name="begin-hour">
            <?php for ($i = 0; $i <= 23; $i++) {
                $select = $i == date('H', $this->contestInfo['begin_time']) ? 'selected' : '';
                ?>
                <option <?php echo $select; ?> value="<?php echo sprintf('%02d', $i); ?>"><?php echo sprintf('%02d', $i); ?></option>
            <?php } ?>
        </select><label class="label">时</label>
        <select <?php echo $this->contestInfo['is_active'] ? 'disabled="true"' : ''; ?> class="select" name="begin-minute">
            <?php for ($i = 0; $i <= 59; $i++) {
                $select = $i == date('i', $this->contestInfo['begin_time']) ? 'selected' : '';
                ?>
                <option <?php echo $select; ?> value="<?php echo sprintf('%02d', $i); ?>"><?php echo sprintf('%02d', $i); ?></option>
            <?php } ?>
        </select><label class="label">分</label>&nbsp;（比赛<span class="red">激活</span>后，无法改变比赛开始时间）
    </div>
    <div class="item">
        <label class="label w80">结束时间：</label>
        <input name="end-date" class="tc input w90" type="text" value="<?php echo $this->contestInfo['end_time'] ? date('Y-m-d', $this->contestInfo['end_time']) : date('Y-m-d', time()); ?>" />&nbsp;
        <select class="select" name="end-hour">
            <?php for ($i = 0; $i <= 23; $i++) {
                $select = $i == date('H', $this->contestInfo['end_time']) ? 'selected' : '';
                ?>
                <option <?php echo $select; ?> value="<?php echo sprintf('%02d', $i); ?>"><?php echo sprintf('%02d', $i); ?></option>
            <?php } ?>
        </select><label class="label">时</label>
        <select class="select" name="end-minute">
            <?php for ($i = 0; $i <= 59; $i++) {
                $select = $i == date('i', $this->contestInfo['end_time']) ? 'selected' : '';
                ?>
                <option <?php echo $select; ?> value="<?php echo sprintf('%02d', $i); ?>"><?php echo sprintf('%02d', $i); ?></option>
            <?php } ?>
        </select><label class="label">分</label>&nbsp;（比赛时间不能超过1年）
    </div>
    <div class="item">
        <label class="label w80">&nbsp;</label>
        <label class="label"><input type="checkbox" class="checkbox" name="problem-hidden" <?php echo $this->contestInfo['problem_hidden'] ? 'checked' : ''; ?> />&nbsp;隐藏题目</label>（勾选后，比赛过程中会隐藏题目的标题）
    </div>
    <div>
        <label class="label w80">描述：</label>
    </div>
    <div>
        <textarea class="textarea-ke" name="description" rows="30" style="width: 1000px;"><?php echo htmlspecialchars($this->contestInfo['description']); ?></textarea>
    </div>
    <div class="mt10 mb10">
        <input name="edit" type="button" class="btn btn-blue w120" value="提交修改" />
    </div>
    <input name="contest-id" type="hidden" value="<?php echo $this->contestInfo['id']; ?>" />
</form>

<script src="//sta.hqoj.net/js/plugin/kindeditor/kindeditor.js"></script>
<script src="//sta.hqoj.net/js/plugin/kindeditor/lang/zh_CN.js"></script>

<script>
    seajs.use(['jquery', 'notice', 'jquery.datepicker'], function($, notice, fn1) {

        fn1($);
        $('input[name=begin-date]').datepicker();
        $('input[name=end-date]').datepicker();

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
            var beginTime = $('input[name=begin-date]').val() + ' ' + $('select[name=begin-hour]').val() + ':' + $('select[name=begin-minute]').val() + ':00';
            var endTime = $('input[name=end-date]').val() + ' ' + $('select[name=end-hour]').val() + ':' + $('select[name=end-minute]').val() + ':00';
            var contestId = $('input[name=contest-id]').val();
            var problemHidden = $('input[name=problem-hidden]').is(':checked') ? 1 : 0;
            $.ajax({
                url: '/setup_contest_edit/ajaxSubmit/',
                type: 'post',
                dataType: 'json',
                data: {
                    'contest-id'    : contestId,
                    'title'         : $('input[name=title]').val(),
                    'type'          : $('input[name=type]:checked').val(),
                    'password'      : $('input[name=password]').val(),
                    'notice'        : $('input[name=notice]').val(),
                    'begin-time'    : beginTime,
                    'end-time'      : endTime,
                    'description'   : $('textarea[name=description]').val(),
                    'problem-hidden': problemHidden
                },
                success: function(result) {
                    if (0 === result.errorCode) {
                        location.href = '/setup_contest_detail/?contest-id=' + contestId;
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