<form method="get" class="mb10 bg-gray widget-form widget-form-toolbar">
    <label class="label">用户名</label>
    <input name="username" class="w150 input" type="text" value="<?php echo Request::getGET('username'); ?>" />
    <label class="label">题目</label>
    <select class="select" name="problem-hash">
        <option value="-1">-</option>
        <?php foreach ($this->contestInfo['problem_hash'] as $globalId => $no) {
            $select = $no == Request::getGET('problem-hash', -1) ? 'selected' : '';
            ?>
            <option <?php echo $select; ?> value="<?php echo $no; ?>"><?php echo $no; ?></option>
        <?php } ?>
    </select>
    <label class="label">语言</label>
    <select name="language" class="select">
        <option value="-1">-</option>
        <?php
        $language = (int) Request::getGET('language', -1);
        foreach (StatusVars::$LANGUAGE_FORMAT as $key => $value) {
            $selected = $language == $key ? 'selected' : '';
            echo sprintf('<option %s value="%s">%s</option>', $selected, $key, $value);
        }
        ?>
    </select>
    <label class="label">结果</label>
    <select name="result" class="select">
        <option value="-1">-</option>
        <?php
        $result = Request::getGET('result', -1);
        foreach (StatusVars::$RESULT_FORMAT as $key => $value) {
            $selected = $result == $key ? 'selected' : '';
            echo sprintf('<option %s value="%s">%s</option>', $selected, $key, $value);
        }
        ?>
    </select>
    <input name="contest-id" value="<?php echo $this->contestInfo['id']; ?>" type="hidden" />
    <input type="submit" class="w100 btn" value="查找" />
</form>

<?php echo $this->html['pager']; ?>

<style>
    .status-link:hover {
        text-decoration: underline;
    }
</style>

<table class="mt10 mb10 widget-table  widget-table-hover status">
    <thead>
    <tr>
        <th class="tc" width="8%">序号</th>
        <th class="tc" width="16%">提交时间</th>
        <th class="tc" width="18%">结果</th>
        <th class="tc" width="10%">题号</th>
        <th class="tc" width="7%">时间</th>
        <th class="tc" width="7%">内存</th>
        <th class="tc" width="7%">代码</th>
        <th class="tc" width="7%">语言</th>
        <th class="tc" width="20%">提交者</th>
    </tr>
    </thead>
    <tbody>
    <?php
    foreach ($this->solutionList as $solutionInfo) {
        $userInfo = $this->userHash[$solutionInfo['user_id']];
        $hashKey = $this->contestInfo['problem_hash'][$solutionInfo['problem_global_id']];
        $applyInfo = empty($this->applyHash) ? array() : $this->applyHash[$userInfo['id']];
        ?>
        <tr>
            <td class="tc"><?php echo $solutionInfo['contest_submit_order']; ?></td>
            <td class="tc">
                <?php echo date('Y-m-d H:i:s', $solutionInfo['submit_time']); ?>
                <?php if ($solutionInfo['result'] == StatusVars::TIME_OUT || $solutionInfo['result'] >= 2 && $this->isContestAdmin) { ?>
                    <a name="user-rejudge" solution-id="<?php echo $solutionInfo['id']; ?>" title="rejudge" href="#"><img src="//sta.hqoj.net/image/www/oj/status_rejudge.png" /></a>
                <?php } ?>
            </td>
            <td class="tc">
                <span class="<?php echo StatusVars::$RESULT_CLASS[$solutionInfo['result']]; ?>"><?php echo StatusVars::$RESULT_FORMAT[$solutionInfo['result']]; ?></span>
                <?php if ($solutionInfo['result'] >= 4 && $solutionInfo['permission'] && $solutionInfo['has_log']) { ?>
                    <a name="show-judge-log" solution-id="<?php echo $solutionInfo['id']; ?>" href="#"><img src="//sta.hqoj.net/image/www/oj/show_log.png" /></a>
                <?php } ?>
            </td>
            <td class="tc">
                <a href="/problem_detail/?contest-id=<?php echo $this->contestInfo['id']; ?>&problem-hash=<?php echo $hashKey; ?>" >
                    <?php echo $hashKey; ?>
                </a>
            </td>
            <td class="tc"><?php echo $solutionInfo['permission'] ? $solutionInfo['time_cost'].'MS' : '-'; ?></td>
            <td class="tc"><?php echo $solutionInfo['permission'] ? $solutionInfo['memory_cost'].'KB' : '-'; ?></td>
            <td class="tc">
                <?php if ($solutionInfo['permission']) { ?>
                    <a href="/status_code/?contest-id=<?php echo $this->contestInfo['id']; ?>&solution-id=<?php echo $solutionInfo['id']; ?>"><?php echo $solutionInfo['code_length']; ?>B</a>
                <?php } else { ?>
                    <?php echo '-'; ?>
                <?php } ?>
            </td>
            <td class="tc"><?php echo StatusVars::$LANGUAGE_FORMAT[$solutionInfo['language']]; ?></td>
            <td class="tc">
                <a href="/user_my/?username=<?php echo $userInfo['username']; ?>">
                    <?php if ($this->isContestAdmin && $this->contestInfo['type'] == ContestVars::TYPE_APPLY) {
                        $tmpUserInfo = $userInfo;
                        $tmpUserInfo['nickname'] = sprintf('%s %s', $applyInfo['xuehao'], $applyInfo['real_name']);
                        ?>
                        <?php echo OjCommonHelper::getColorName($tmpUserInfo);; ?>
                    <?php } else { ?>
                        <?php echo OjCommonHelper::getColorName($userInfo); ?>
                    <?php } ?>
                </a>
            </td>
        </tr>
    <?php } ?>
    </tbody>
</table>

<?php echo $this->html['pager']; ?>

<script>
    seajs.use(['jquery', 'notice', 'layer'], function($, notice, layer) {

        $('a[name=user-rejudge]').click(function(e) {
            e.preventDefault();
            $this = $(this);
            $.ajax({
                url: '/status_list/ajaxRejudge/',
                type: 'post',
                dataType: 'json',
                data: {
                    'solution-id' : $(this).attr('solution-id'),
                    'contest-id': $('input[name=contest-id]').val()
                },
                success: function(result) {
                    if (0 === result.errorCode) {
                        $this.parent().next().html('<span class="green">Queue（Rejudge）</span>');
                        $this.remove();
                    } else {
                        notice('error', result.errorMessage);
                    }
                },
                error: function() {
                    notice('error', '服务器请求失败！');
                }
            });
        });

        var flag = false;
        $('a[name=show-judge-log]').click(function(e) {
            e.preventDefault();
            if (flag) {
                return false;
            }
            flag = true;
            var url = '/status_judgeLog/iframeShow/?solution-id='+$(this).attr('solution-id')+'&contest-id='+$('input[name=contest-id]').val();
            $.layer({
                type: 2,
                title: 'JUDGE LOG',
                border: [1, 1, '#ddd'],
                shade: [0],
                iframe: {src : url },
                area: ['800px' , '450px'],
                shift: 'top',
                close: function(index){
                    flag = false;
                }
            });
        });

    });
</script>
