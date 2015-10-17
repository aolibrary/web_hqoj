<div class="p10 mb10 border f14 tc">
    <p>竞赛：<?php echo $this->contestInfo['title']; ?></p>
</div>

<form class="mb10 bg-gray widget-form widget-form-toolbar" method="get">
    <div class="fl">
        <label class="label">状态：</label>
        <select class="select" name="status">
            <option value="-1">ALL</option>
            <?php   foreach (ContestVars::$APPLY_FORMAT as $key => $value) {
                        $selected = Request::getGET('status', -1) == $key ? 'selected' : '';
            ?>
                <option <?php echo $selected; ?> value="<?php echo $key; ?>"><?php echo $value; ?></option>
            <?php   } ?>
        </select>
        <input type="hidden" name="contest-id" value="<?php echo Request::getGET('contest-id'); ?>" />
        <input class="w80 btn" type="submit" value="查找" />
    </div>
    <div style="clear:both"></div>
</form>

<?php echo $this->html['pager']; ?>

<table class="mt10 mb10 f12 widget-table widget-table-hover">
    <thead>
        <tr>
            <th width="20%">&nbsp;&nbsp;报名时间</th>
            <th width="20%">昵称</th>
            <th class="tc" width="20%">性别</th>
            <th width="30%">学院</th>
            <th width="10%">审核结果</th>
        </tr>
    </thead>
    <tbody>
        <?php   foreach ($this->applyList as $applyInfo) {
                    $userInfo   = $this->userHash[$applyInfo['user_id']];
        ?>
        <tr>
            <td>&nbsp;&nbsp;<?php echo date('Y-m-d H:i:s', $applyInfo['create_time']); ?></td>
            <td><a href="/user_my/?username=<?php echo $userInfo['username']; ?>"><?php echo OjCommonHelper::getColorName($userInfo); ?></a></td>
            <td class="tc"><?php echo $applyInfo['sex'] == 1 ? '男' : '女'; ?></td>
            <td><?php echo ContestVars::$XUEYUAN[$applyInfo['xueyuan']]; ?></td>
            <td><span class="<?php echo ContestVars::$APPLY_COLOR[$applyInfo['status']]; ?>" ><?php echo ContestVars::$APPLY_FORMAT[$applyInfo['status']]; ?></span></td>
        </tr>
        <?php } ?>
    </tbody>
</table>
