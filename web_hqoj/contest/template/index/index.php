<div style="padding-top: 10px;">
    <div class="tc f18 mb10"><?php echo $this->contestInfo['title']; ?></div>
    <div class="tc mb10">比赛时间：<?php echo date('Y-m-d H:i:s', $this->contestInfo['begin_time']) . ' - ' . date('Y-m-d H:i:s', $this->contestInfo['end_time']); ?></div>
    <div class="tc" style="margin-bottom: 10px;">
        <?php if (time() < $this->contestInfo['begin_time']) { ?>
            <span class="green">比赛未开始</span>
        <?php } else if (time() > $this->contestInfo['end_time']) { ?>
            <span class="gray">比赛已结束</span>
        <?php } else { ?>
            <span class="red">比赛进行中</span>
        <?php } ?>
    </div>
    
    <div class="tc orange" style="margin-bottom: 20px;">当前时间：<?php echo date('Y-m-d H:i:s', time()); ?></div>
    
    <?php if ($this->contestInfo['type'] == ContestVars::TYPE_APPLY) { ?>
        <form class="widget-form w600" style="margin: 0 auto;">
            <div class="item">
                <label class="label w160">比赛权限：</label>
                <label class="label"><?php echo sprintf('<span class="%s">%s</span>', ContestVars::$TYPE_COLOR[$this->contestInfo['type']], ContestVars::$TYPE_FORMAT[$this->contestInfo['type']]); ?></label>
            </div>
            <?php if (!empty($this->applyInfo)) { ?>
                <div class="item">
                    <label class="label w160">我的报名信息：</label>
                    <label class="label"><?php echo $this->applyInfo['xuehao'] . ' - ' . $this->applyInfo['real_name'] . ' - '. ($this->applyInfo['sex'] == 1 ? '男' : '女') . ' - ' . ContestVars::$XUEYUAN[$this->applyInfo['xueyuan']]; ?></label>
                </div>
                <div class="item">
                    <label class="label w160">报名审核：</label>
                    <label class="label"><span class="<?php echo ContestVars::$APPLY_COLOR[$this->applyInfo['status']]; ?>" ><?php echo ContestVars::$APPLY_FORMAT[$this->applyInfo['status']]; ?></span></label>
                </div>
            <?php } else { ?>
                <div class="item">
                    <label class="label w160">我的报名信息：</label>
                    <label class="label">未报名</label>
                </div>
            <?php } ?>
            
        </form>
    <?php } else if ($this->contestInfo['type'] == ContestVars::TYPE_PASSWORD) { ?>
        <form class="widget-form w600" style="margin: 0 auto;">
            <div class="item">
                <label class="label w160">比赛权限：</label>
                <label class="label"><?php echo sprintf('<span class="%s">%s</span>', ContestVars::$TYPE_COLOR[$this->contestInfo['type']], ContestVars::$TYPE_FORMAT[$this->contestInfo['type']]); ?></label>
            </div>
            <div class="item">
                <label class="label w160">密码校验：</label>
                <?php if ($this->password != $this->contestInfo['password']) { ?>
                    <input class="input w220" type="text" name="password" />
                    <input class="btn btn-blue w80" type="submit" name="pass-submit" value="输入" />
                <?php } else { ?>
                    <label class="label green">已通过校验</label>
                <?php }?>
            </div>
        </form>
    <?php } ?>
</div>

<input name="contest-id" type="hidden" value="<?php echo $this->contestInfo['id']; ?>" />

<div style="margin: 20px auto; width: 980px;">
    <?php echo $this->contestInfo['description']; ?>
</div>

<script>
    seajs.use(['jquery', 'notice'], function($, notice) {
        
        $('input[name=pass-submit]').click(function(e) {
            e.preventDefault();
            // 校验登录
            $.ajax({
                url: '/index/ajaxSetPassword/',
                type: 'post',
                dataType: 'json',
                data: {
                    'password': $('input[name=password]').val(),
                    'contest-id': $('input[name=contest-id]').val()
                },
                success: function(result) {
                    if (0 === result.errorCode) {
                        location.reload();
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