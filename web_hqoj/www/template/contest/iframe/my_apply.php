<div class="p10">
    <form id="form" class="widget-form" style="border:0;">
        <div class="item">
            <label class="w120 label">参加竞赛：</label>
            <label class="fw label"><?php echo $this->contestInfo['title']; ?></label>
        </div>
        <div class="item">
            <label class="w120 label">姓名 <span class="red">*</span>：</label>
            <input name="real-name" class="input w180" value="<?php echo $this->preApplyInfo['real_name']; ?>" type="text" data-validation="length" data-validation-length="2-4" data-validation-error-msg="2-4位" />
        </div>
        <div class="item">
            <label class="w120 label">学号 <span class="red">*</span>：</label>
            <input name="xuehao" class="input w180" value="<?php echo $this->preApplyInfo['xuehao']; ?>" type="text" data-validation="required" />
        </div>
        <div class="item">
            <label class="w120 label">性别 <span class="red">*</span>：</label>
            <select name="sex" class="w190 select" data-validation="number" data-validation-allowing="range[1;2]" data-validation-error-msg="请选择性别">
                <option value="-1"> - </option>
                <option value="1" <?php echo $this->preApplyInfo['sex'] == 1 ? 'selected' : ''; ?> >男</option>
                <option value="2" <?php echo $this->preApplyInfo['sex'] == 2 ? 'selected' : ''; ?> >女</option>
            </select>
        </div>
        <div class="item">
            <label class="w120 label">学院 <span class="red">*</span>：</label>
            <select class="select w190" name="xueyuan" data-validation="number" data-validation-allowing="range[1;100]" data-validation-error-msg="请选择学院">
                <option value="-1"> - </option>
                <?php   foreach (ContestVars::$XUEYUAN as $key => $value) {
                            $selected = $this->preApplyInfo['xueyuan'] == $key ? 'selected' : '';
                ?>
                <option <?php echo $selected; ?> value="<?php echo $key; ?>"><?php echo $value; ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="item">
            <label class="w120 label">审核结果：</label>
            <?php if (empty($this->applyInfo)) { ?>
                <label class="fw label">未报名</label>
            <?php } else { ?>
                <label class="fw label <?php echo ContestVars::$APPLY_COLOR[$this->applyInfo['status']]; ?>" ><?php echo ContestVars::$APPLY_FORMAT[$this->applyInfo['status']]; ?></label>
            <?php } ?>
        </div>
        <div class="item">
            <?php if (empty($this->applyInfo)) { ?>
                <input class="ml130 btn btn-blue w90" type="submit" name="submit" value="报名" />
            <?php } else if ($this->applyInfo['status'] != ContestVars::APPLY_ACCEPTED) { ?>
                <input class="ml130 btn btn-blue w90" type="submit" name="submit" value="重新申请" />
            <?php } else { ?>
                <input class="ml130 btn w90" type="button" disabled="true" value="已报名" />
            <?php }?>
            <input class="btn w90" type="button" name="cancel" value="关闭" />
        </div>
        <input name="contest-id" type="hidden" value="<?php echo $this->contestInfo['id']; ?>" />
    </form>
</div>

<script>
    seajs.use(['jquery', 'layer', 'notice', 'jquery.form-validator'], function($, layer, notice, fn1) {
        
        fn1($);
        
        var index = parent.layer.getFrameIndex(window.name);
        
        $('input[name=cancel]').click(function() {
            parent.layer.close(index);
        });
        
        $.validate({
            form : '#form',
            onSuccess : function() {
                
                $.ajax({
                    url: '/contest_myApply/ajaxApply/',
                    type: 'post',
                    dataType: 'json',
                    data: {
                        'contest-id':   $('input[name=contest-id]').val(),
                        'real-name':    $('input[name=real-name]').val(),
                        'xuehao':       $('input[name=xuehao]').val(),
                        'xueyuan':      $('select[name=xueyuan]').val(),
                        'sex':          $('select[name=sex]').val()
                    },
                    success: function(result) {
                        if (0 === result.errorCode) {
                            notice('success', '申请成功！', 1, function() {
                                parent.location.reload();
                            });
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
