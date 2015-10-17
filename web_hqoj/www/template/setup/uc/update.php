<form id="form" class="widget-form">
    <div class="item">
        <label class="label w120">个性头像：</label>
        <img style="border: 1px solid #ddd;" id="pic-img" src="<?php echo OjCommonHelper::getHeadUrl($this->loginUserInfo['head_img'], $this->loginUserInfo['sex']); ?>" width="100px" height="100px" />
        <a id="uploader-btn" href="#" return="false">[更换头像]</a>
        <span class="gray">&nbsp;只允许jpg,jpeg,bmp,png,gif格式，大小不超过100KB。</span>
    </div>
    <div class="item">
        <label class="label w120"><span class="red">* </span>昵称：</label>
        <input name="nickname" value="<?php echo $this->loginUserInfo['nickname']; ?>" class="input w220" type="text" data-validation="length" data-validation-length="1-16" data-validation-error-msg="8个汉字或者16个英文字符以内" />
    </div>
    <div class="item">
        <label class="label w120">个性签名：</label>
        <input name="motto" value="<?php echo $this->loginUserInfo['motto']; ?>" class="input w600" type="text" data-validation="length" data-validation-length="max100" />
    </div>
    <div class="item">
        <label class="w120 label"><span class="red">* </span>性别：</label>
        <label class="label"><input <?php echo $this->loginUserInfo['sex'] == 1 ? 'checked' : ''; ?> value="1" name="sex" type="radio" class="radio" /> 男</label>
        <label class="label"><input <?php echo $this->loginUserInfo['sex'] == 2 ? 'checked' : ''; ?> value="2" name="sex" type="radio" class="radio" /> 女</label>
    </div>
    <div class="item">
        <label class="w120 label"><span class="red">* </span>代码设置：</label>
        <label class="label"><input <?php echo $this->loginUserInfo['share'] ? 'checked' : ''; ?> value="1" name="share" type="radio" class="radio" /> 公开</label>
        <label class="label"><input <?php echo !$this->loginUserInfo['share'] ? 'checked' : ''; ?> value="0" name="share" type="radio" class="radio" /> 私有</label>
    </div>
    <div class="item">
        <input class="btn btn-blue w100 ml130" type="submit" value="修改" />
        <label id="progress" class="label"></label>
    </div>
</form>

<script>
    seajs.use(['jquery', 'jquery.form-validator', 'js/util/upload/Upload.js', 'notice'], function($, fn1, Uploader, notice) {

        fn1($);

        var uploader = new Uploader({

            trigger: '#uploader-btn',
            action: '/setup_uc_update/ajaxUpload/',
            accept: 'image/*',
            multiple: false,

            progress: function(e, position, total, percent) {
                var $progress = $('#progress');
                $progress.html(' 上传中，请勿操作...' + percent + '%');
                if (percent == 100) {
                    $progress.html(' <img src="//sta.hqoj.net/image/common/loading/loading03.gif" height="22px" />&nbsp;上传头像中...');
                }
            },

            change: function(fileList) {

                if (fileList[0].type != 'image/png'
                    && fileList[0].type != 'image/jpg'
                    && fileList[0].type != 'image/jpeg'
                    && fileList[0].type != 'image/bmp'
                    && fileList[0].type != 'image/gif'
                ) {
                    notice('error', '只允许jpg,jpeg,bmp,png,gif格式的图片！');
                    return false;
                }

                if (fileList[0].size > 102400) {
                    notice('error', '图片大小不能超过100KB！');
                    return false;
                }

                this.submit();
            },

            success: function(response) {

                $('#progress').html('');

                var result = '';
                try {
                    result = $.parseJSON(response);
                } catch (e) {
                    notice('error', '服务器请求失败！');
                    return false;
                }

                if (0 === result.errorCode) {
                    notice('success', '头像设置成功！');
                    $('#pic-img').attr('src', result.src);
                } else {
                    notice('error', result.errorMessage);
                }
            },

            error: function(data) {
                $('#progress').html('');
                notice('error', '服务器请求失败！' + data.statusText);
            }
        });

        $.validate({
            form : '#form',
            onSuccess : function() {

                $.ajax({
                    url: '/setup_uc_update/ajaxSubmit/',
                    type: 'post',
                    dataType: 'json',
                    data: {
                        'nickname' : $('input[name=nickname]').val(),
                        'motto' : $('input[name=motto]').val(),
                        'share' : $('input[name=share]:checked').val(),
                        'sex' : $('input[name=sex]:checked').val()
                    },
                    success: function(result) {
                        if (0 === result.errorCode) {
                            notice('success', '修改资料成功！');
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