<div class="p10 tc border">专题标题：<?php echo $this->setInfo['title']; ?></div>
<div class="p10 mt10 tc border">只能添加题库中的题目，无法添加私有题目；每个专题的题数上限为<span class="red">50</span>道；</div>

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
    <input name="add-problem" type="submit" class="btn btn-blue w150" value="添加到专题" />
    <input type="hidden" name="set-id" value="<?php echo $this->setInfo['id']; ?>" />
</form>

<table class="mt10 mb10 widget-table widget-table-hover f12">
    <thead>
        <tr>
            <th class="tc" width="16%">题目</th>
            <th width="35%">标题</th>
            <th width="35%">来源</th>
            <th width="14%">操作</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($this->problemHash as $id => $problemInfo) { ?>
        <tr>
            <td class="tc"><?php echo $problemInfo['remote'] ? StatusVars::$REMOTE_SCHOOL[$problemInfo['remote']] : ''; echo $problemInfo['problem_code']; ?></td>
            <td>
                <a href="/problem_detail/?global-id=<?php echo $problemInfo['id']; ?>"><?php echo $problemInfo['title']; ?></a>
            </td>
            <td><?php echo $problemInfo['source']; ?></td>
            <td>
                <a set-id="<?php echo $this->setInfo['id']; ?>" global-id="<?php echo $problemInfo['id']; ?>" name="remove" href="#" >移除</a>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>

<script>
    seajs.use(['jquery', 'notice'], function($, notice) {
        
        $('input[name=add-problem]').click(function(e) {
            e.preventDefault();
            $.ajax({
                url: '/setup_set_problem/ajaxAddProblem/',
                type: 'post',
                dataType: 'json',
                data: {
                    'remote': $('select[name="remote"]').val(),
                    'problem-code': $('input[name=problem-code]').val(),
                    'set-id': $('input[name=set-id]').val()
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
                url: '/setup_set_problem/ajaxRemoveProblem/',
                type: 'post',
                dataType: 'json',
                data: {
                    'set-id': $(this).attr('set-id'),
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