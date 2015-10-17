<form class="mb10 bg-gray widget-form widget-form-toolbar" method="get">
    <div class="fl">
        <label class="label">题库</label>
        <select class="select" name="remote">
            <?php   foreach (StatusVars::$REMOTE_SCHOOL as $remote => $code) {
                        $select = $remote == Request::getGET('remote') ? 'selected' : '';
            ?>
                <option <?php echo $select; ?> value="<?php echo $remote; ?>"><?php echo $code; ?></option>
            <?php } ?>
        </select>
        <label class="label">&nbsp;标题</label>
        <input class="w250 input" type="text" name="title" value="<?php echo Request::getGET('title'); ?>" />
        <label class="label">&nbsp;状态</label>
        <select class="select" name="status">
            <option value="-1">-</option>
            <option <?php echo Request::getGET('status') == 1? 'selected' : ''; ?> value="1">公开</option>
            <option <?php echo Request::getGET('status') == 2? 'selected' : ''; ?> value="2">私有</option>
        </select>
        <input class="w80 btn" type="submit" value="查找" />
    </div>
    <div class="fr">
        <input name="add" class="w100 btn btn-blue" type="button" value="新建题目" />
    </div>
    <div style="clear:both"></div>
</form>

<?php echo $this->html['pager']; ?>

<table class="mt10 mb10 f12 widget-table widget-table-hover">
    <thead>
        <tr>
            <th width="8%" class="tc">题号</th>
            <th width="30%">标题</th>
            <th width="28%">分类</th>
            <th width="12%">出题人</th>
            <th width="6%" class="tc">状态</th>
            <th width="16%">操作</th>
        </tr>
    </thead>
    <tbody>
        <?php   foreach ($this->problemList as $problemInfo) {
                    $userInfo = $this->userHash[$problemInfo['user_id']];
        ?>
        <tr>
            <td class="tc">
                <a href="/problem_detail/?global-id=<?php echo $problemInfo['id']; ?>">
                <?php if ($problemInfo['remote']) { ?>
                    <?php echo StatusVars::$REMOTE_SCHOOL[$problemInfo['remote']] . $problemInfo['problem_code']; ?>
                <?php } else { ?>
                    <?php echo $problemInfo['problem_id']; ?>
                <?php } ?>
                </a>
            </td>
            <td>
                <p><a href="/problem_detail/?global-id=<?php echo $problemInfo['id']; ?>"><?php echo empty($problemInfo['title']) ? '-' : $problemInfo['title']; ?></a></p>
            </td>
            <td>
                <p><?php echo $problemInfo['source']; ?></p>
            </td>
            <td>
                <p><?php echo $userInfo['username']; ?></p>
            </td>
            <td class="tc">
                <?php echo $problemInfo['hidden'] ? '<span class="green">私有</span>' : '<span class="red">公开</span>'; ?>
            </td>
            <td>
                <?php if ($problemInfo['hidden']) { ?>
                <a name="show" global-id="<?php echo $problemInfo['id']; ?>" href="#">公开</a>
                <?php } else { ?>
                    <a name="hide" global-id="<?php echo $problemInfo['id']; ?>" href="#">隐藏</a>
                <?php } ?>
                <?php if ($problemInfo['remote'] == StatusVars::REMOTE_HQU) { ?>
                    <a name="set-user" href="#" global-id="<?php echo $problemInfo['id']; ?>">更换出题人</a>
                    <a href="#" name="data-manager" problem-id="<?php echo $problemInfo['problem_id']; ?>">数据</a>
                    <a name="show-history" problem-id="<?php echo $problemInfo['problem_id']; ?>" href="#">日志</a>
                <?php } ?>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>

<script>
    seajs.use(['jquery', 'layer', 'notice'], function($, layer, notice) {
        
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
        
        $('input[name=add]').click(function(e) {
            e.preventDefault();
            $.layer({
                area: ['auto','auto'],
                dialog: {
                    msg: '你确定要创建题目？',
                    btns: 2,
                    type: 4,
                    btn: ['确定', '取消'],
                    yes: function() {
                        $.ajax({
                            url: '/problem_add/ajaxAdd/',
                            type: 'post',
                            dataType: 'json',
                            data: {
                                
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
                    }, no: function() {
                        
                    }
                }
            });
        });
        
        $('a[name=show]').click(function(e) {
            e.preventDefault();
            $.ajax({
                url: '/problem_list/ajaxShow/',
                type: 'post',
                dataType: 'json',
                data: {
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
        
        $('a[name=hide]').click(function(e) {
            e.preventDefault();
            $.ajax({
                url: '/problem_list/ajaxHide/',
                type: 'post',
                dataType: 'json',
                data: {
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
        
        $('a[name=set-user]').click(function(e) {
            e.preventDefault();
            var url = '/problem_list/iframeSetUser/?global-id=' + $(this).attr('global-id');
            $.layer({
                type: 2,
                title: '指定负责人',
                iframe: {src : url },
                area: ['600px' , '200px']
            });
        });
        
        $('a[name=show-history]').click(function(e) {
            e.preventDefault();
            var title = $(this).attr('problem-id')+' 题目操作记录';
            var url = '/problem_list/iframeShowHistory/?problem-id=' + $(this).attr('problem-id');
            $.layer({
                type: 2,
                title: title,
                iframe: {src : url },
                area: ['550px' , '300px'],
                shift: 'top',
                close: function(index) {}
            });
        });

        $('select[name=remote]').change(function(e) {
            var url = '/problem_list/?remote=' + $(this).val();
            location.href = url;
        });
    });
</script>