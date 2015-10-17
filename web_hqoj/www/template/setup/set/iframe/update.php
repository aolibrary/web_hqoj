<form id="form" class="widget-form mt10" style="border: 0;">
    <div class="item">
        <label class="w120 label">专题名称：</label>
        <input value="<?php echo $this->setInfo['title']; ?>" name="title" class="w400 input" type="text" />
    </div>
    <div class="item">
        <label class="w120 label">刷新时间：</label>
        <input name="refresh-day" class="tc input w90" type="text" value="<?php echo $this->setInfo['refresh_at'] ? date('Y-m-d', $this->setInfo['refresh_at']) : date('Y-m-d', time()); ?>" />&nbsp;
        <select class="select" name="hour">
            <?php for ($i = 0; $i <= 23; $i++) {
                    $select = $i == date('H', $this->setInfo['refresh_at']) ? 'selected' : '';
            ?>
                <option <?php echo $select; ?> value="<?php echo sprintf('%02d', $i); ?>"><?php echo sprintf('%02d', $i); ?></option>
            <?php } ?>
        </select><label class="label">时</label>
        <select class="select" name="minute">
            <?php for ($i = 0; $i <= 59; $i++) {
                    $select = $i == date('i', $this->setInfo['refresh_at']) ? 'selected' : '';
            ?>
                <option <?php echo $select; ?> value="<?php echo sprintf('%02d', $i); ?>"><?php echo sprintf('%02d', $i); ?></option>
            <?php } ?>
        </select><label class="label">分</label>
    </div>
    <div class="item">
        <label class="w120 label">&nbsp;</label>
        <label class="label red">专题训练中将根据刷新时间排序，刷新时间不能超过当天</label>
    </div>
    <div class="item">
        <input name="submit" class="ml130 w110 btn btn-blue" type="submit" value="修改" />
        <input name="cancel" class="w110 btn" type="button" value="取消" />
    </div>
    <input name="set-id" type="hidden" value="<?php echo $this->setInfo['id']; ?>" />
</form>

<script>
    seajs.use(['jquery', 'notice', 'layer', 'jquery.datepicker'], function($, notice, layer, fn2) {
        
        fn2($);
        
        $('input[name=refresh-day]').datepicker();
        $('input[name=refresh-day]').datepicker('option', 'maxDate', new Date()); 
        
        var index = parent.layer.getFrameIndex(window.name);
        
        $('input[name=cancel]').click(function() {
            parent.updateIframeOpen = false;
            parent.layer.close(index);
        });
        
        $('input[name=submit]').click(function(e) {
            e.preventDefault();
            var refreshAt = $('input[name=refresh-day]').val() + ' ' + $('select[name=hour]').val() + ':' + $('select[name=minute]').val() + ':00';
            $.ajax({
                url: '/setup_set_update/ajaxSubmit/',
                type: 'post',
                dataType: 'json',
                data: {
                    'title': $('input[name=title]').val(),
                    'set-id': $('input[name=set-id]').val(),
                    'refresh-at': refreshAt
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
        
    });
</script>