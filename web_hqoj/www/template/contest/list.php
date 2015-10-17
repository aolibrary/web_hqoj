<div class="p10 tc f16 fw"><?php echo Request::getGET('diy', 0) ? 'DIY比赛' : '标准竞赛'; ?></div>

<form class="mt10 mb10 bg-gray widget-form widget-form-toolbar" method="get">
    <div class="fl">
        <label class="label">比赛标题</label>
        <input class="w250 input" type="text" name="title" value="<?php echo Request::getGET('title'); ?>" />
        <input type="hidden" name="uri" value="<?php echo Url::getPath(); ?>" />
        <input class="w80 btn" type="submit" value="查找" />
    </div>
    <div class="fr">
        <?php if (Url::getPath() == '/diy_list/') { ?>
            <a href="/setup_contest_list/" class="f12" style="display: inline-block; padding-top: 6px;" >&lt;&lt; 我要创建DIY比赛<span class="fw red">【HOT】</span></a>&nbsp;
        <?php } ?>
        <label class="label">比赛进行状态</label>
        <select class="select" name="passed">
            <option <?php echo (int) Request::getGET('passed', 0) == 0? 'selected' : ''; ?> value="0">当前</option>
            <option <?php echo (int) Request::getGET('passed', 0) == 1? 'selected' : ''; ?> value="1">已结束</option>
        </select>
    </div>
    <div style="clear:both"></div>
</form>

<?php echo $this->html['pager']; ?>

<style>
    .pending { }
    .running { background-color: #FFEBEF; }
    .passed  { background-color: #f5f5f5; }
</style>

<table class="mt10 mb10 f12 widget-table">
    <thead>
    <tr>
        <th class="tc" width="8%">竞赛ID</th>
        <th width="37%">标题</th>
        <th width="13%">时间</th>
        <th class="tc" width="26%">比赛权限</th>
        <th width="16%">负责人</th>
    </tr>
    </thead>
    <tbody>
    <?php   foreach ($this->contestList as $contestInfo) {
        $userInfo = $this->userHash[$contestInfo['user_id']];
        ?>
        <tr class="<?php echo $contestInfo['row_css']; ?>">
            <td class="tc"><?php echo $contestInfo['id']; ?></td>
            <td>
                <p><a href="//contest.hqoj.net/?contest-id=<?php echo $contestInfo['id']; ?>"><?php echo $contestInfo['title']; ?></a></p>
            </td>
            <td>
                <p><?php echo date('Y-m-d H:i:s', $contestInfo['begin_time']); ?></p>
                <p><?php echo date('Y-m-d H:i:s', $contestInfo['end_time']); ?></p>
            </td>
            <td class="tc">
                <?php echo $contestInfo['type_format']; ?>
                <?php if ($contestInfo['type'] == ContestVars::TYPE_APPLY) { ?>（
                    <?php if ($contestInfo['end_time'] > time()) { ?>
                        <a href="#" contest-id="<?php echo $contestInfo['id']; ?>" name="apply" >点此报名</a> |
                    <?php } ?>
                    <a href="/contest_applyList/?contest-id=<?php echo $contestInfo['id']; ?>">报名列表</a> ）
                <?php } ?>
            </td>
            <td><a href="/user_my/?username=<?php echo $userInfo['username']; ?>"><?php echo OjCommonHelper::getColorName($userInfo); ?></a></td>
        </tr>
    <?php } ?>
    </tbody>
</table>

<script>
    seajs.use(['jquery', 'layer', 'notice'], function($, layer, notice) {

        $('a[name=apply]').click(function(e) {
            e.preventDefault();
            var applyUrl = '/contest_myApply/iframeApply/?contest-id=' + $(this).attr('contest-id');
            $.layer({
                type: 2,
                title: '我要报名！！！',
                iframe: {src : applyUrl },
                shade: [0],
                border: [5, 1, '#E7E7E7'],
                area: ['600px' , '380px']
            });
        });

        $('select[name=passed]').change(function(e) {
            e.preventDefault();
            var url = $('input[name=uri]').val();
            if (1 == $(this).val()) {
                url = url+'?passed=1';
            }
            location.href = url;
        });
    });
</script>