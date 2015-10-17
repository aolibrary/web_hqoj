<form class="widget-form mt10" style="border: 0;">
    <div class="item">
        <label class="w120 label">分类：</label>
        <select name="category" class="select w230">
            <?php
                foreach (DocVars::$CATEGORY as $key => $value) {
                    $selected = $key == $this->docInfo['category'] ? 'selected' : '';
            ?>
                <option <?php echo $selected; ?> value="<?php echo $key; ?>"><?php echo $value; ?></option>
            <?php } ?>
        </select>
    </div>
    <div class="item">
        <label class="w120 label">负责人：</label>
        <input name="username" class="w220 input" type="text" value="<?php echo $this->userInfo['username']; ?>" />
    </div>
    <div class="item">
        <input name="submit" class="ml130 btn btn-blue w110" type="submit" value="修改" />
        <input name="cancel" class="btn w110" type="submit" value="取消" />
    </div>
    <input type="hidden" name="doc-id" value="<?php echo Request::getGET('doc-id'); ?>" />
</form>

<script>
    seajs.use(['jquery', 'notice'], function($, notice) {
        
        var index = parent.layer.getFrameIndex(window.name);
        
        $('input[name=cancel]').click(function(e) {
            e.preventDefault();
            parent.layer.close(index);
        });
        
        $('input[name=submit]').click(function(e) {
            e.preventDefault();
            $.ajax({
                url: '/edit/ajaxChange/',
                type: 'post',
                dataType: 'json',
                data: {
                    'username': $('input[name=username]').val(),
                    'doc-id': $('input[name=doc-id]').val(),
                    'category': $('select[name=category]').val()
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