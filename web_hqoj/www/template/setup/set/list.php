<div class="tc border p10">
    <p>HQOJ问题反馈&疑难解答：<span class="red fw">347832779</span>（QQ群）</p>
</div>

<form class="bg-gray widget-form widget-form-toolbar mt10 mb10">
    <div class="fl">
        <label class="label">专题：</label>
        <input name="title" class="input w300" type="text" value="<?php echo Request::getGET('title'); ?>" />
        <label class="label">状态：</label>
        <select class="select" name="status">
            <option value="-1">-</option>
            <option <?php echo Request::getGET('status') == 1? 'selected' : ''; ?> value="1">公开</option>
            <option <?php echo Request::getGET('status') == 2? 'selected' : ''; ?> value="2">自己可见</option>
        </select>
        <input class="w80 btn" type="submit" value="查找" />
    </div>
    <div class="fr">
        <input name="add-set" class="w120 btn btn-blue" type="button" value="添加专题" />
    </div>
    <div style="clear:both;"></div>
</form>

<?php echo $this->html['pager']; ?>

<table id="problem-list-table" class="widget-table widget-table-hover mt10 f12">
    <thead>
        <tr>
            <th width="10%" class="tc">ID</th>
            <th width="35%">专题名称</th>
            <th class="tc" width="10%">题数</th>
            <th width="20%">刷新时间</th>
            <th widht="10%">状态</th>
            <th width="15%">操作</th>
        </tr>
    </thead>
    <tbody>
        <?php   foreach ($this->setList as $setInfo) { ?>
        <tr>
            <td class="tc"><?php echo $setInfo['id']; ?></td>
            <td><a href="/set_problem/?set-id=<?php echo $setInfo['id']; ?>"><?php echo $setInfo['title'] ? $setInfo['title'] : '-'; ?></a></td>
            <td class="tc"><?php echo $setInfo['count']; ?></td>
            <td><?php echo $setInfo['refresh_at'] ? date('Y-m-d H:i:s', $setInfo['refresh_at']) : '-'; ?></td>
            <td>
                <?php echo $setInfo['hidden'] ? '<span class="green">自己可见</span>' : '<span class="red">公开</span>'; ?>
            </td>
            <td>
                <a href="/setup_set_problem/?set-id=<?php echo $setInfo['id']; ?>">管理题目</a>
                <a set-id="<?php echo $setInfo['id']; ?>" name="update" href="#">编辑</a>
                <?php   if ($setInfo['hidden']) { ?>
                    <a set-id="<?php echo $setInfo['id']; ?>" name="show" href="#">显示</a>
                <?php   } else { ?>
                    <a set-id="<?php echo $setInfo['id']; ?>" name="hide" href="#">隐藏</a>
                <?php   } ?>
            </td>
        </tr>
        <?php   } ?>
    </tbody>
</table>

<script>
    seajs.use(['jquery', 'layer', 'notice'], function($, layer, notice) {
        
        $('input[name=add-set]').click(function(e) {
            e.preventDefault();
            $.layer({
                shade: [0],
                area: ['auto','auto'],
                dialog: {
                    msg: '你确定要创建专题？',
                    btns: 2,
                    type: 4,
                    btn: ['确定', '取消'],
                    yes: function() {
                        $.ajax({
                        url: '/setup_set_add/ajaxSubmit/',
                        type: 'post',
                        dataType: 'json',
                        data: {
                            
                        },
                        success: function(result) {
                            if (0 === result.errorCode) {
                                location.href = '/setup_set_list/';
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
        
        updateIframeOpen = false;
        $('a[name=update]').click(function(e) {
            e.preventDefault();
            if (updateIframeOpen) {
                return false;
            }
            updateIframeOpen = true;
            var url = '/setup_set_update/iframeUpdate/?set-id='+$(this).attr('set-id');
            $.layer({
                type: 2,
                title: '修改专题信息',
                iframe: {src : url },
                border: [5, 1, '#E7E7E7'],
                shade: [0],
                area: ['700px' , '260px'],
                close: function(index) {
                    updateIframeOpen = false;
                }
            });
        });
        
        $('a[name=show]').click(function(e) {
            e.preventDefault();
            $.ajax({
                url: '/setup_set_list/ajaxShow/',
                type: 'post',
                dataType: 'json',
                data: {
                    'set-id': $(this).attr('set-id')
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
            var $this = $(this);
            $.ajax({
                url: '/setup_set_list/ajaxHide/',
                type: 'post',
                dataType: 'json',
                data: {
                    'set-id': $(this).attr('set-id')
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


