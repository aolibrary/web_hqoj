<div class="mb10 p10 tc f16 fw">实时状态</div>

<form method="get" class="mb10 bg-gray widget-form widget-form-toolbar">
    <label class="label">用户名</label>
    <input name="username" class="w150 input" type="text" value="<?php echo Request::getGET('username'); ?>" />
    <label class="label">题目</label>
    <select class="select" name="remote">
        <option value="-1">-</option>
        <?php foreach (StatusVars::$REMOTE_SCHOOL as $remote => $code) {
            $select = $remote == Request::getGET('remote', -1) ? 'selected' : '';
            ?>
            <option <?php echo $select; ?> value="<?php echo $remote; ?>"><?php echo $code; ?></option>
        <?php } ?>
    </select>
    <input name="problem-code" class="w80 input" type="text" value="<?php echo Request::getGET('problem-code'); ?>" />
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
    <input type="submit" class="w100 btn" value="查找" />
</form>

<?php echo $this->html['pager']; ?>

<style>
    .widget-table tbody tr {
        height: 45px;
    }
</style>

<table class="mt10 mb10 widget-table widget-table-hover status">
    <thead>
    <tr>
        <th class="tc" width="7%">ID</th>
        <th class="tc" width="17%">提交时间</th>
        <th class="tc" width="18%">结果</th>
        <th class="tc" width="10%">题号</th>
        <th class="tc" width="7%">时间</th>
        <th class="tc" width="7%">内存</th>
        <th class="tc" width="7%">代码</th>
        <th class="tc" width="7%">语言</th>
        <th width="6%">权限</th>
        <th class="tc" width="14%">提交者</th>
    </tr>
    </thead>
    <tbody>
    <?php
    foreach ($this->solutionList as $solutionInfo) {
        $userId = $solutionInfo['user_id'];
        $userInfo = $this->userHash[$userId]
        ?>
        <tr>
            <td class="tc"><?php echo $solutionInfo['id']; ?></td>
            <td class="tc">
                <?php echo date('Y-m-d H:i:s', $solutionInfo['submit_time']); ?>
                <?php if ($solutionInfo['result'] == StatusVars::TIME_OUT || $solutionInfo['result'] >= 2 && $this->isOjAdmin) { ?>
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
                <a href="/problem_detail/?global-id=<?php echo $solutionInfo['problem_global_id']; ?>" >
                    <?php echo $solutionInfo['remote'] ? StatusVars::$REMOTE_SCHOOL[$solutionInfo['remote']] : ''; echo $solutionInfo['problem_code']; ?>
                </a>
            </td>
            <td class="tc"><?php echo $solutionInfo['time_cost']; ?>MS</td>
            <td class="tc"><?php echo $solutionInfo['memory_cost']; ?>KB</td>
            <td class="tc">
                <?php if ($solutionInfo['permission']) { ?>
                    <a href="/status_code/?solution-id=<?php echo $solutionInfo['id']; ?>"><?php echo $solutionInfo['code_length']; ?>B</a>
                <?php } else { ?>
                    <span><?php echo $solutionInfo['code_length']; ?>B</span>
                <?php } ?>
            </td>
            <td class="tc"><?php echo StatusVars::$LANGUAGE_FORMAT[$solutionInfo['language']]; ?></td>
            <td>
                <?php echo sprintf('<span class="%s">%s</span>', StatusVars::$LEVEL_COLOR[$solutionInfo['level']], StatusVars::$LEVEL_FORMAT[$solutionInfo['level']]); ?>
                <?php if ($solutionInfo['user_id'] == Arr::get('id', $this->loginUserInfo, 0)) { ?>
                    <a name="helping" title="求助" solution-id="<?php echo $solutionInfo['id']; ?>" title="rejudge" href="#"><img src="//sta.hqoj.net/image/www/oj/status_rejudge.png" /></a>
                <?php } ?>
            </td>
            <td class="tc"><a href="/user_my/?username=<?php echo $userInfo['username']; ?>"><?php echo OjCommonHelper::getColorName($userInfo); ?></a></td>
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
                    'solution-id' : $(this).attr('solution-id')
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

        $('a[name=helping]').click(function(e) {
            e.preventDefault();
            $.ajax({
                url: '/status_list/ajaxHelp/',
                type: 'post',
                dataType: 'json',
                data: {
                    'solution-id' : $(this).attr('solution-id')
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

        var flag = false;
        $('a[name=show-judge-log]').click(function(e) {
            e.preventDefault();
            if (flag) {
                return false;
            }
            flag = true;
            var url = '/status_judgeLog/iframeShow/?solution-id='+$(this).attr('solution-id');
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
