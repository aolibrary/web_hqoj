<div class="tc border p10">
    <p>每个用户的私有题目上限为<span class="red">15</span>道，公开的题目没有限制。&nbsp;HQOJ问题反馈&疑难解答：<span class="red fw">347832779</span>（QQ群）</p>
</div>
<form class="mt10 mb10 bg-gray widget-form widget-form-toolbar" method="get">
    <div class="fl">
        <label class="label">状态：</label>
        <select class="select" name="status">
            <option value="-1">-</option>
            <option <?php echo Request::getGET('status') == 1? 'selected' : ''; ?> value="1">公开</option>
            <option <?php echo Request::getGET('status') == 2? 'selected' : ''; ?> value="2">私有</option>
        </select>
        <input class="w80 btn" type="submit" value="查找" />
    </div>
    <div class="fr">
        <input name="add" class="w120 btn btn-blue" type="button" value="创建题目" />
    </div>
    <div style="clear:both"></div>
</form>

<?php echo $this->html['pager']; ?>

<table class="mt10 mb10 f12 widget-table widget-table-hover">
    <thead>
    <tr>
        <th width="6%" class="tc">题号</th>
        <th width="35%">标题</th>
        <th width="35%">分类</th>
        <th width="10%" class="tc">状态</th>
        <th width="14%">操作</th>
    </tr>
    </thead>
    <tbody>
    <?php   foreach ($this->problemList as $problemInfo) {
        $userInfo = $this->userHash[$problemInfo['user_id']];
        ?>
        <tr>
            <td class="tc">
                <a href="/setup_problem_detail/?global-id=<?php echo $problemInfo['id']; ?>"><?php echo $problemInfo['problem_id']; ?></a>
            </td>
            <td>
                <a href="/setup_problem_detail/?global-id=<?php echo $problemInfo['id']; ?>"><?php echo Arr::get('title', $problemInfo, '-', true); ?></a>
            </td>
            <td><?php echo $problemInfo['source']; ?></td>
            <td class="tc">
                <?php echo $problemInfo['hidden'] ? '<span class="green">私有</span>' : '<span class="red">公开</span>'; ?>
            </td>
            <td>
                <?php if ($problemInfo['hidden']) { ?>
                    <a href="/setup_problem_edit/?global-id=<?php echo $problemInfo['id']; ?>">编辑</a>
                    <a href="#" name="data-manager" problem-id="<?php echo $problemInfo['problem_id']; ?>">管理数据</a>
                <?php } ?>
            </td>
        </tr>
    <?php } ?>
    </tbody>
</table>

<script>
    seajs.use(['jquery', 'layer', 'notice'], function($, layer, notice) {

        $('input[name=add]').click(function(e) {
            e.preventDefault();
            $.layer({
                shade: [0],
                area: ['auto','auto'],
                dialog: {
                    msg: '每个人允许同时创建最多15道私有题目，你确定要创建题目？',
                    btns: 2,
                    type: 4,
                    btn: ['确定', '取消'],
                    yes: function() {
                        $.ajax({
                            url: '/setup_problem_add/ajaxAdd/',
                            type: 'post',
                            dataType: 'json',
                            data: {},
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
                    }, no: function() {

                    }
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