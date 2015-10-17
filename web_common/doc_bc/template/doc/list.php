<form class="widget-form widget-form-toolbar bg-gray mb10">
    <div class="fl">
        <label class="label">标题：</label>
        <input name="title" class="input w220" type="text" value="<?php echo Request::getGET('title'); ?>" />
        <label class="label">负责人：</label>
        <input name="username" class="input w120" type="text" value="<?php echo Request::getGET('username'); ?>" />
        <input type="submit" class="w100 btn" value="查找" />
    </div>
    <div class="fr">
        <input name="add" category="<?php echo Request::getGET('category', 0); ?>" type="button" class="w150 btn btn-blue" value="添加一篇文章" />
    </div>
    <div style="clear:both;"></div>
</form>

<?php echo $this->html['pager']; ?>

<table class="widget-table widget-table-hover mt10">
    <thead>
    <tr>
        <th width="8%" class="tc">文章ID</th>
        <th width="40%">标题</th>
        <th width="20%">类别</th>
        <th width="12%">负责人</th>
        <th width="5%">状态</th>
        <th width="15%">操作</th>
    </tr>
    </thead>
    <tbody>
    <?php   foreach ($this->docList as $docInfo) {
        $userInfo = $this->userHash[$docInfo['user_id']];
        ?>
        <tr>
            <td class="tc"><?php echo $docInfo['id']; ?></td>
            <td>
                <p><a href="/detail/?doc-id=<?php echo $docInfo['id']; ?>"><?php echo $docInfo['title'] ? $docInfo['title'] : ' - '; ?></a></p>
                <p>展示地址:
                    <a style="color:#333;" href="<?php echo DocHelper::getDocUrl($docInfo['id']); ?>" target="_blank">
                        <?php echo DocHelper::getDocUrl($docInfo['id']); ?>
                    </a>
                </p>
            </td>
            <td><?php echo Arr::get($docInfo['category'], DocVars::$CATEGORY, '其他'); ?></td>
            <td><?php echo $userInfo['username']; ?></td>
            <td><?php echo $docInfo['hidden'] ? '<span class="green">隐藏</span>' : '<span class="red">显示</span>'; ?></td>
            <td>
                <?php if ($docInfo['hidden']) { ?>
                    <a name="show" doc-id="<?php echo $docInfo['id']; ?>" href="#">显示</a>
                <?php } else { ?>
                    <a name="hide" doc-id="<?php echo $docInfo['id']; ?>" href="#">隐藏</a>
                <?php } ?>
                <a name="change" href="#" doc-id="<?php echo $docInfo['id']; ?>">管理</a>
                <a href="/edit/?doc-id=<?php echo $docInfo['id']; ?>">编辑</a>
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
                    msg: '新建一篇文章？',
                    btns: 2,
                    type: 4,
                    btn: ['确定', '取消'],
                    yes: function() {
                        $.ajax({
                            url: '/add/ajaxAdd/',
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

        $('a[name=show]').click(function(e) {
            e.preventDefault();
            $.ajax({
                url: '/list/ajaxShow/',
                type: 'post',
                dataType: 'json',
                data: {
                    'doc-id': $(this).attr('doc-id')
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
                url: '/list/ajaxHide/',
                type: 'post',
                dataType: 'json',
                data: {
                    'doc-id': $(this).attr('doc-id')
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

        $('a[name=change]').click(function(e) {
            e.preventDefault();
            var url = '/edit/iframeChange/?doc-id=' + $(this).attr('doc-id');
            $.layer({
                type: 2,
                title: '管理',
                iframe: {src : url },
                area: ['600px' , '220px'],
                close: function(index){

                }
            });
        });

    });
</script>