<div class="p10 tc border">竞赛标题：<?php echo $this->contestInfo['title']; ?></div>
<div class="p10 mt10 tc border">您可以添加题库中的题目，以及自己创建的私有题目；比赛题数上限为26道，无法移除已经有人提交的题目；</div>

<form class="widget-form widget-form-toolbar bg-gray mt10 mb10">
    <label class="label w100">题目</label>
    <select class="select" name="remote">
        <option value="-1">-</option>
        <?php   foreach (StatusVars::$REMOTE_SCHOOL as $remote => $code) {
                    $selected = Cookie::get('default_remote', -1) == $remote ? 'selected' : '';
        ?>
            <option <?php echo $selected; ?> value="<?php echo $remote; ?>"><?php echo $code; ?></option>
        <?php } ?>
    </select>
    <input name="problem-code" class="w80 input" type="text"  />
    <input name="add-problem" type="submit" class="btn btn-blue w120" value="添加到竞赛" />
    <input type="hidden" name="contest-id" value="<?php echo $this->contestInfo['id']; ?>" />
</form>

<table class="mt10 mb10 widget-table widget-table-hover f12">
    <thead>
        <tr>
            <th class="tc" width="6%">字母</th>
            <th class="tc" width="10%">题号</th>
            <th width="36%">标题</th>
            <th width="36%">来源</th>
            <th width="12%">操作</th>
        </tr>
    </thead>
    <tbody>
        <?php   foreach ($this->globalIds as $globalId) {
                    $problemInfo = $this->problemHash[$globalId];
        ?>
        <tr>
            <td class="tc"><?php echo $this->contestInfo['problem_hash'][$globalId]; ?></td>
            <td class="tc"><?php echo $problemInfo['remote'] ? StatusVars::$REMOTE_SCHOOL[$problemInfo['remote']] : ''; echo $problemInfo['problem_code']; ?></td>
            <td>
                <?php if ($problemInfo['remote']) { ?>
                    <a href="//www.hqoj.net/problem_detail/?global-id=<?php echo $problemInfo['id']; ?>"><?php echo $problemInfo['title']; ?></a>
                <?php } else { ?>
                    <a href="/problem_detail/?global-id=<?php echo $problemInfo['id']; ?>"><?php echo $problemInfo['title']; ?></a>
                <?php } ?>
            </td>
            <td><?php echo $problemInfo['source']; ?></td>
            <td>
                <?php if (!in_array($problemInfo['id'], $this->submitGlobalIds)) { ?>
                <a contest-id="<?php echo $this->contestInfo['id']; ?>" global-id="<?php echo $problemInfo['id']; ?>" name="remove" href="#" >移除</a>
                <?php } ?>
            </td>
        </tr>
        <?php   } ?>
    </tbody>
</table>

<script>
    seajs.use(['jquery', 'notice'], function($, notice) {
        
        $('input[name=add-problem]').click(function(e) {
            e.preventDefault();
            $.ajax({
                url: '/contest_setProblem/ajaxAddProblem/',
                type: 'post',
                dataType: 'json',
                data: {
                    'remote': $('select[name="remote"]').val(),
                    'problem-code': $('input[name=problem-code]').val(),
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
        
        $('a[name=remove]').click(function(e) {
            e.preventDefault();
            $.ajax({
                url: '/contest_setProblem/ajaxRemoveProblem/',
                type: 'post',
                dataType: 'json',
                data: {
                    'contest-id': $(this).attr('contest-id'),
                    'global-id': $(this).attr('global-id')
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
    })
</script>