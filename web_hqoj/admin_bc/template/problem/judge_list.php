<form method="get" class="mb10 bg-gray widget-form widget-form-toolbar">
    <label class="label">用户名</label>
    <input name="username" class="w150 input" type="text" value="<?php echo Request::getGET('username'); ?>" />
    <label class="label">题号</label>
    <input name="problem-id" class="w80 input" type="text" value="<?php echo Request::getGET('problem-id'); ?>" />
    <label class="label">语言</label>
    <select name="language" class="select">
        <option value="-1">-</option>
        <?php
            $language = (int) Request::getGET('language', -1);
            foreach (StatusVars::$LANGUAGE_SUPPORT[StatusVars::REMOTE_HQU] as $key => $value) {
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
    .status th, .status td {
        text-align: center;
    }
</style>

<table class="mt10 mb10 widget-table widget-table-hover status f12">
    <thead>
        <tr>
            <th width="8%">ID</th>
            <th width="18%">评判时间</th>
            <th width="20%">结果</th>
            <th width="12%">题号</th>
            <th width="7%">时间</th>
            <th width="7%">内存</th>
            <th width="7%">代码</th>
            <th width="7%">语言</th>
            <th width="14%">用户名</th>
        </tr>
    </thead>
    <tbody>
        <?php
            foreach ($this->judgeList as $judgeInfo) {
                $userId = $judgeInfo['user_id'];
                $userInfo = $this->userHash[$userId];
        ?>
        <tr>
            <td>
                <?php if ($judgeInfo['solution_id']) { ?>
                    <span class="red"><?php echo $judgeInfo['solution_id']; ?></span>
                <?php } else { ?>
                    <span class="green"><?php echo $judgeInfo['id']; ?></span>
                <?php } ?>
            </td>
            <td>
                <?php echo $judgeInfo['judge_time'] ? date('Y-m-d H:i:s', $judgeInfo['judge_time']) : '-'; ?>
                <?php if ($judgeInfo['result'] >= 2) { ?>
                    <a name="user-rejudge" judge-id="<?php echo $judgeInfo['id']; ?>" title="rejudge" href="#"><img src="//sta.hqoj.net/image/www/oj/status_rejudge.png" /></a>
                <?php } ?>
            </td>
            <td>
                <span class="<?php echo StatusVars::$RESULT_CLASS[$judgeInfo['result']]; ?>"><?php echo $judgeInfo['result_html']; ?></span>
                <?php if ($judgeInfo['result'] >= 2) { ?>
                    <a name="show-judge-log" judge-id="<?php echo $judgeInfo['id']; ?>" href="#"><img src="//sta.hqoj.net/image/www/oj/show_log.png" /></a>
                <?php } ?>
                
            </td>
            <td>
                <span><a href="/problem_detail/?problem-id=<?php echo $judgeInfo['problem_id']; ?>" ><?php echo $judgeInfo['problem_id']; ?></a></span>
                <span>（<a href="#" name="data-manager" problem-id="<?php echo $judgeInfo['problem_id']; ?>">数据</a>）</span>
            </td>
            <td><?php echo $judgeInfo['time_cost']; ?>MS</td>
            <td><?php echo $judgeInfo['memory_cost']; ?>KB</td>
            <td><a href="/problem_code/?judge-id=<?php echo $judgeInfo['id']; ?>"><?php echo $judgeInfo['code_length']; ?>B</a></td>
            <td><?php echo StatusVars::$LANGUAGE_FORMAT[$judgeInfo['language']]; ?></td>
            <td><a href="//www.hqoj.net/user_my/?username=<?php echo $userInfo['username']; ?>"><?php echo $userInfo['username']; ?></a></td>
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
                url: '/problem_judgeList/ajaxRejudge/',
                type: 'post',
                dataType: 'json',
                data: {
                    'judge-id' : $(this).attr('judge-id')
                },
                success: function(result) {
                    if (0 === result.errorCode) {
                        $this.parent().next().html('<span class="green">Queue（Rejudge）</span>');
                        $this.parent().text('-');
                    } else {
                        notice('error', result.errorMessage);
                    }
                },
                error: function() {
                    notice('error', '服务器请求失败！');
                }
            });
        });
        
        $('a[name=show-judge-log]').click(function(e) {
            e.preventDefault();
            var url = '/problem_judgeLog/iframeShow/?judge-id='+$(this).attr('judge-id');
            $.layer({
                type: 2,
                title: 'JUDGE LOG',
                iframe: {src : url },
                area: ['800px' , '450px'],
                shift: 'top',
                close: function(index) {}
            });
        });
        
        $('a[name=data-manager]').click(function(e) {
            e.preventDefault();
            var title = $(this).attr('problem-id');
            var url = '/problem_dataManager/iframeManager/?problem-id=' + $(this).attr('problem-id');
            $.layer({
                type: 2,
                title: title,
                iframe: {src : url },
                area: ['850px' , '490px'],
                shift: 'top',
                close: function(index) {}
            });
        });
    });
</script>
