<div class="tc border p10">在这里你可以对题目进行严格的测试。</div>
<form method="get" class="mt10 mb10 bg-gray widget-form widget-form-toolbar">
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
            <th width="6%">RUN ID</th>
            <th width="20%">评判时间</th>
            <th width="22%">结果</th>
            <th width="12%">题号</th>
            <th width="10%">时间</th>
            <th width="10%">内存</th>
            <th width="10%">代码</th>
            <th width="10%">语言</th>
        </tr>
    </thead>
    <tbody>
        <?php
            foreach ($this->judgeList as $judgeInfo) {
                $userId = $judgeInfo['user_id'];
                $userInfo = $this->userHash[$userId];
        ?>
        <tr>
            <td><?php echo $judgeInfo['id']; ?></td>
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
                <span><a href="/setup_problem_detail/?problem-id=<?php echo $judgeInfo['problem_id']; ?>" ><?php echo $judgeInfo['problem_id']; ?></a></span>
                <span>（<a href="#" name="data-manager" problem-id="<?php echo $judgeInfo['problem_id']; ?>">数据</a>）</span>
            </td>
            <td><?php echo $judgeInfo['time_cost']; ?>MS</td>
            <td><?php echo $judgeInfo['memory_cost']; ?>KB</td>
            <td><a href="/setup_problem_code/?judge-id=<?php echo $judgeInfo['id']; ?>"><?php echo $judgeInfo['code_length']; ?>B</a></td>
            <td><?php echo StatusVars::$LANGUAGE_FORMAT[$judgeInfo['language']]; ?></td>
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
                url: '/setup_problem_judgeList/ajaxRejudge/',
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
        
        var flag = false;
        $('a[name=show-judge-log]').click(function(e) {
            e.preventDefault();
            if (flag) {
                return false;
            }
            flag = true;
            var url = '/setup_problem_judgeLog/iframeShow/?judge-id='+$(this).attr('judge-id');
            $.layer({
                type: 2,
                title: 'JUDGE LOG',
                border: [1, 1, '#ddd'],
                shade: [0],
                iframe: {src : url },
                area: ['800px' , '450px'],
                close: function(index) {
                    flag = false;
                }
            });
        });
        
        dataIframeOpen = false;
        $('a[name=data-manager]').click(function(e) {
            e.preventDefault();
            if (dataIframeOpen) {
                return false;
            }
            dataIframeOpen = true;
            var title = $(this).attr('problem-id');
            var url = '/setup_problem_dataManager/iframeManager/?problem-id=' + $(this).attr('problem-id');
            $.layer({
                type: 2,
                title: title,
                border: [5, 1, '#E7E7E7'],
                shade: [0],
                iframe: {src : url },
                area: ['850px' , '490px'],
                close: function(index) {
                    dataIframeOpen = false;
                }
            });
        });
    });
</script>
