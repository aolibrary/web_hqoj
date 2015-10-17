<style>
    .widget-form .item-1 .help-block {
        margin: 5px 0 0 130px;
        width: 400px;
    }
</style>

<form id="form" method="post" class="widget-form mt10 mb10">
    <div class="item">
        <label class="ml130 label" style="font-size: 16px;">提交 <span class="f18 fw"><?php echo $this->contestInfo['problem_hash'][$this->problemInfo['id']]; ?></span> 题</label>
    </div>
    <div class="item">
        <label class="w120 label"><span class="red"> * </span>选择编译器</label>
        <select name="language" class="w160 select" data-validation="number" data-validation-allowing="range[1;100]" data-validation-error-msg="请选择">
            <option value="-1">----</option>
            <?php   foreach (StatusVars::$LANGUAGE_SUPPORT[$this->problemInfo['remote']] as $key => $value) {
                $selected = Cookie::get('default_language', StatusVars::CXX) == $key ? 'selected' : '';
                ?>
                <option <?php echo $selected; ?> value="<?php echo $key; ?>"><?php echo $value; ?></option>
            <?php   } ?>
        </select>
    </div>
    <div class="item item-1">
        <label class="w120 label"><span class="red"> * </span>代码</label>
        <textarea class="w800 textarea" name="code" rows="25" data-validation="length" data-validation-length="50-65535" data-validation-error-msg="代码长度超出范围，请限制为50-65535Byte"></textarea>
    </div>
    <div class="item">
        <input class="ml130 w120 btn btn-blue" type="submit" value="提交代码" />
    </div>
    <input name="problem-hash" type="hidden" value="<?php echo $this->contestInfo['problem_hash'][$this->problemInfo['id']]; ?>" />
    <input name="contest-id" type="hidden" value="<?php echo $this->contestInfo['id']; ?>" />
    <input name="status-url" type="hidden" value="/status_list/?contest-id=<?php echo $this->contestInfo['id']; ?>" />
</form>

<script>
    seajs.use(['jquery', 'jquery.form-validator', 'notice'], function($, fn1, notice) {

        fn1($);

        $.validate({
            'form' : '#form',
            'onSuccess' : function() {
                $.ajax({
                    url: '/problem_submit/ajaxSubmit/',
                    type: 'post',
                    dataType: 'json',
                    data: {
                        'problem-hash': $('input[name=problem-hash]').val(),
                        'language': $('select[name=language]').val(),
                        'code': $('textarea[name=code]').val(),
                        'contest-id': $('input[name=contest-id]').val()
                    },
                    success: function(result) {
                        if (0 === result.errorCode) {
                            location.href = $('input[name=status-url]').val();
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