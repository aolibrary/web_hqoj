<div class="tc border p10">
    <p>HQOJ问题反馈&疑难解答：<span class="red fw">347832779</span>（QQ群）</p>
</div>

<form class="mt10 mb10 bg-gray widget-form widget-form-toolbar" method="get">
    <div class="fl">
        <label class="label">标题：</label>
        <input class="w250 input" type="text" name="title" value="<?php echo Request::getGET('title'); ?>" />
        <label class="label">状态：</label>
        <select class="select" name="status">
            <option value="-1">-</option>
            <option <?php echo Request::getGET('status') == 1? 'selected' : ''; ?> value="1">显示</option>
            <option <?php echo Request::getGET('status') == 2? 'selected' : ''; ?> value="2">隐藏</option>
        </select>
        <input class="w80 btn" type="submit" value="查找" />
    </div>
    <div class="fr">
        <input name="add" class="w120 btn btn-blue" type="button" value="创建DIY比赛" />
    </div>
    <div style="clear:both"></div>
</form>

<?php echo $this->html['pager']; ?>

<table class="mt10 mb10 f12 widget-table widget-table-hover">
    <thead>
        <tr>
            <th class="tc" width="10%">竞赛ID</th>
            <th width="45%">标题</th>
            <th class="tc" width="10%">比赛权限</th>
            <th class="tc" width="10%">激活</th>
            <th class="tc" width="10%">状态</th>
            <th width="15%">操作</th>
        </tr>
    </thead>
    <tbody>
        <?php   foreach ($this->contestList as $contestInfo) {
                    $userInfo = $this->userHash[$contestInfo['user_id']];
        ?>
        <tr>
            <td class="tc"><?php echo $contestInfo['id']; ?></td>
            <td>
                <p><a href="/setup_contest_detail/?contest-id=<?php echo $contestInfo['id']; ?>"><?php echo Arr::get('title', $contestInfo, '-', true); ?></a></p>
                <p>时间：<?php echo date('Y-m-d H:i:s', $contestInfo['begin_time']) . ' - ' . date('Y-m-d H:i:s', $contestInfo['end_time']); ?></p>
            </td>
            <td class="tc">
                <p><?php echo sprintf('<span class="%s">%s</span>', ContestVars::$TYPE_COLOR[$contestInfo['type']], ContestVars::$TYPE_FORMAT[$contestInfo['type']]); ?></p>
                <p><?php echo $contestInfo['type'] == ContestVars::TYPE_PASSWORD ? $contestInfo['password'] : ''; ?></p>
            </td>
            <td class="tc">
                <?php echo $contestInfo['is_active'] ? '<span class="red">是</span>' : ' - '; ?>
            </td>
            <td class="tc">
                <?php echo $contestInfo['hidden'] ? '<span class="green">隐藏</span>' : '<span class="red">显示</span>'; ?>
            </td>
            <td>
                <a href="/setup_contest_edit/?contest-id=<?php echo $contestInfo['id']; ?>">编辑</a>
                <a href="/setup_contest_setProblem/?contest-id=<?php echo $contestInfo['id']; ?>">管理题目</a>
                <?php if ($contestInfo['hidden']) { ?>
                <a name="show" contest-id="<?php echo $contestInfo['id']; ?>" href="#">显示</a><br/>
                <?php } else { ?>
                    <a name="hide" contest-id="<?php echo $contestInfo['id']; ?>" href="#">隐藏</a>
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
                    msg: '你确定要创建DIY竞赛？',
                    btns: 2,
                    type: 4,
                    btn: ['确定', '取消'],
                    yes: function() {
                        $.ajax({
                        url: '/setup_contest_add/ajaxAdd/',
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
                url: '/setup_contest_list/ajaxShow/',
                type: 'post',
                dataType: 'json',
                data: {
                    'contest-id': $(this).attr('contest-id')
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
                url: '/setup_contest_list/ajaxHide/',
                type: 'post',
                dataType: 'json',
                data: {
                    'contest-id': $(this).attr('contest-id')
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
        
    });
</script>