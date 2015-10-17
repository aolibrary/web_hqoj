<form class="bg-gray widget-form widget-form-toolbar mb10">
    <label class="label">专题名称：</label>
    <input name="title" class="input w300" type="text" value="<?php echo Request::getGET('title'); ?>" />
    <label class="label">创建者：</label>
    <input name="username" class="input w180" type="text" value="<?php echo Request::getGET('username'); ?>" />
    <input class="w80 btn" type="submit" value="查找" />
</form>

<?php echo $this->html['pager']; ?>

<table id="problem-list-table" class="widget-table widget-table-hover mt10 mb10">
    <thead>
    <tr>
        <th width="8%" class="tc">专题ID</th>
        <th width="35%">专题名称</th>
        <th class="tc" width="5%">题数</th>
        <th width="18%">刷新时间</th>
        <th width="10%">状态</th>
        <th width="16%">创建者</th>
        <th width="8%">操作</th>
    </tr>
    </thead>
    <tbody>
    <?php   foreach ($this->setList as $setInfo) {
        $userId = $setInfo['user_id'];
        $userInfo = $this->userHash[$userId];
        ?>
        <tr>
            <td class="tc"><?php echo $setInfo['id']; ?></td>
            <td>
                <?php echo $setInfo['listing_status'] ? '<span class="red fw">[顶]</span>' : ''; ?>
                <a href="//www.hqoj.net/set_problem/?set-id=<?php echo $setInfo['id']; ?>"><?php echo $setInfo['title']; ?></a>
            </td>
            <td class="tc"><?php echo $setInfo['count']; ?></td>
            <td><?php echo date('Y-m-d H:i:s', $setInfo['refresh_at']); ?></td>
            <td>
                <?php echo $setInfo['hidden'] ? '<span class="green">自己可见</span>' : '<span class="red">公开</span>'; ?>
            </td>
            <td><a href="//www.hqoj.net/user_my/?username=<?php echo $userInfo['username']; ?>"><?php echo OjCommonHelper::getColorName($userInfo); ?></a></td>
            <td>
                <?php if ($setInfo['listing_status']) { ?>
                    <a href="#" name="stick-cancel" set-id="<?php echo $setInfo['id']; ?>" >取消置顶</a>
                <?php } else { ?>
                    <a href="#" name="stick" set-id="<?php echo $setInfo['id']; ?>" >置顶</a>
                <?php } ?>
            </td>
        </tr>
    <?php   } ?>
    </tbody>
</table>

<script>
    seajs.use(['jquery', 'layer', 'notice'], function($, layer, notice) {

        $('a[name=stick]').click(function(e) {
            e.preventDefault();
            $.ajax({
                url: '/set_list/ajaxStick/',
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

        $('a[name=stick-cancel]').click(function(e) {
            e.preventDefault();
            var $this = $(this);
            $.ajax({
                url: '/set_list/ajaxStickCancel/',
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

