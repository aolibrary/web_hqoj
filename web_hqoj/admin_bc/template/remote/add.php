<form class="bg-gray widget-form widget-form-toolbar">
    <div class="fl">
        <label class="label">题库</label>
        <select class="select" name="remote">
            <?php foreach (StatusVars::$REMOTE_SCHOOL as $remote => $code) {
                    if ($remote == StatusVars::REMOTE_HQU) {
                        continue;
                    }
                    $select = $remote == Request::getGET('remote') ? 'selected' : '';
            ?>
                <option <?php echo $select; ?> value="<?php echo $remote; ?>"><?php echo $code; ?></option>
            <?php } ?>
        </select>
        <label class="label">向后抓取</label>
        <select class="select" name="number">
            <option value="20">20</option>
            <option value="50">50</option>
            <option value="100">100</option>
        </select>
        <label class="label">题</label>
        <input name="onload" class="w80 btn" type="button" value="加载" />
    </div>
    <div class="fr">
        <input name="add" class="w120 btn btn-blue" type="button" value="添加到题库" />
    </div>
    <div style="clear:both;"></div>
</form>

<div class="mt10 ml5 green">加载的列表中显示的ID/CODE/标题若为空，说明题目不存在或加载失败，这些题号并不会加入到题库当中。</div>

<table id="problem-list-table" class="widget-table widget-table-hover mt10 f12" problem-json="">
    <thead>
        <tr>
            <th width="8%">题库</th>
            <th width="8%">CODE</th>
            <th width="8%">ID</th>
            <th width="38%">标题</th>
            <th width="38%">来源</th>
        </tr>
    </thead>
    <tbody>
        
    </tbody>
</table>

<div id="onloading-div" class="tc" style="margin: 50px auto; display:none;">
    <img src="//sta.hqoj.net/image/common/loading/loading01.gif" />
    <div class="tc f14">加载中...</div>
</div>

<div id="msg-div" class="tc" style="margin: 50px auto; display:none">
    <div id="message" class="tc f14"></div>
</div>

<script>
    seajs.use(['jquery', 'notice'], function($, notice) {
        
        $('input[name=onload]').click(function(e) {
            e.preventDefault();
            $('#msg-div').hide();

            var $problemListTable = $('#problem-list-table');
            $problemListTable.attr('problem-json', '');
            $problemListTable.find('tbody').html('');
            $('#onloading-div').show();
            $.ajax({
                url: '/remote_add/ajaxLoadProblem/',
                type: 'post',
                dataType: 'json',
                data: {
                    'remote': $('select[name=remote]').val(),
                    'number': $('select[name=number]').val()
                },
                success: function(result) {
                    $('#onloading-div').hide();
                    if (0 === result.errorCode) {
                        var innerHtml = '';
                        var problemList = result.problemList;
                        for (i = 0; i < problemList.length; i++) {
                            innerHtml += '<tr>';
                            innerHtml += '<td>'+problemList[i].remote_format+'</td>';
                            innerHtml += '<td>'+problemList[i].problem_code+'</td>';
                            innerHtml += '<td>'+problemList[i].problem_id+'</td>';
                            innerHtml += '<td>'+problemList[i].title+'</td>';
                            innerHtml += '<td>'+problemList[i].source+'</td>';
                            innerHtml += '</tr>';
                        }
                        $problemListTable.attr('problem-json', $.toJSON(problemList));
                        $problemListTable.find('tbody').html(innerHtml);
                    } else {
                        $('#message').html(result.errorMessage);
                        $('#msg-div').show();
                    }
                },
                error: function() {
                    $('#onloading-div').hide();
                    $('#message').html('服务器请求失败！');
                    $('#msg-div').show();
                }
            });
        });
        
        $('input[name=add]').click(function(e) {
            e.preventDefault();
            var problemJson = $('#problem-list-table').attr('problem-json');
            if (!problemJson) {
                notice('error', '请先加载题目！');
                return false;
            }
            $.ajax({
                url: '/remote_add/ajaxAddProblem/',
                type: 'post',
                dataType: 'json',
                data: {
                    'problem-json': problemJson
                },
                success: function(result) {
                    if (0 === result.errorCode) {
                        notice('success', '题目添加成功！', 1, function() {
                            var url = '/remote_add/?remote=' + $('select[name=remote]').val();
                            location.href = url;
                        });
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