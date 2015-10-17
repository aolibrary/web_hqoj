<form class="mb10 bg-gray widget-form widget-form-toolbar" method="get">
    <div class="fl">
        <label class="label">报名状态：</label>
        <select class="select" name="status">
            <option value="-1">ALL</option>
            <?php   foreach (ContestVars::$APPLY_FORMAT as $key => $value) {
                $selected = Request::getGET('status', -1) == $key ? 'selected' : '';
                ?>
                <option <?php echo $selected; ?> value="<?php echo $key; ?>"><?php echo $value; ?></option>
            <?php   } ?>
        </select>
        <label class="label">&nbsp;竞赛ID：</label>
        <input class="w80 input" type="text" name="contest-id" value="<?php echo Request::getGET('contest-id', ''); ?>" />
        <input class="w80 btn" type="submit" value="查找" />
    </div>
    <div style="clear:both"></div>
</form>

<?php echo $this->html['pager']; ?>

<table class="mt10 mb10 f12 widget-table widget-table-hover">
    <thead>
    <tr>
        <th class="tc" width="10%">报名编号</th>
        <th width="50%">报名情况</th>
        <th width="10%">报名时间</th>
        <th class="tc" width="10%">状态</th>
        <th width="15%">操作</th>
    </tr>
    </thead>
    <tbody>
    <?php   foreach ($this->applyList as $applyInfo) {
        $applyUserInfo   = $this->userHash[$applyInfo['user_id']];
        $contestInfo     = $this->contestHash[$applyInfo['contest_id']];
        ?>
        <tr>
            <td class="tc"><?php echo $applyInfo['id']; ?></td>
            <td>
                <p>竞赛ID：<?php echo $contestInfo['id']; ?>&nbsp;&nbsp;<a href="/setup_contest_detail/?contest-id=<?php echo $contestInfo['id']; ?>"><?php echo $contestInfo['title']; ?></a></p>
                <p><?php echo $applyUserInfo['username'] . ' - ' . $applyInfo['xuehao'] . ' - ' . $applyInfo['real_name'] . ' - '. ($applyInfo['sex'] == 1 ? '男' : '女') . ' - ' . ContestVars::$XUEYUAN[$applyInfo['xueyuan']]; ?></p>
            </td>
            <td>
                <p><?php echo date('Y-m-d', $applyInfo['create_time']); ?></p>
                <p><?php echo date('H:i:s', $applyInfo['create_time']); ?></p>
            </td>
            <td class="tc">
                <font class="<?php echo ContestVars::$APPLY_COLOR[$applyInfo['status']]; ?>" ><?php echo ContestVars::$APPLY_FORMAT[$applyInfo['status']]; ?></font>
            </td>
            <td>
                <a href="#" name="change-status" apply-id="<?php echo $applyInfo['id']; ?>" op="1" >通过</a>
                <a href="#" name="change-status" apply-id="<?php echo $applyInfo['id']; ?>" op="2" >拒绝</a>
            </td>
        </tr>
    <?php } ?>
    </tbody>
</table>

<script>
    seajs.use(['jquery', 'layer', 'notice'], function($, layer, notice) {

        $('a[name=change-status]').click(function(e) {
            e.preventDefault();
            $.ajax({
                url: '/setup_contest_applyList/ajaxChangeStatus/',
                type: 'post',
                dataType: 'json',
                data: {
                    'apply-id': $(this).attr('apply-id'),
                    'op': $(this).attr('op')
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