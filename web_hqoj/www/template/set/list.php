<div class="mb10 p10 tc f16 fw">专题训练</div>

<form class="bg-gray widget-form widget-form-toolbar mt10 mb10">
    <div class="fl">
        <label class="label">专题名称：</label>
        <input name="title" class="input w300" type="text" value="<?php echo Request::getGET('title'); ?>" />
        <label class="label">创建者：</label>
        <input name="username" class="input w180" type="text" value="<?php echo Request::getGET('username'); ?>" />
        <input class="w80 btn" type="submit" value="查找" />
    </div>
    <div class="fr">
        <a href="/setup_set_list/" class="f12" style="display: inline-block; padding-top: 6px;" ><span class="fw red">【HOT】</span>我要创建专题 &gt;&gt;</a>
    </div>
    <div style="clear:both"></div>
</form>

<?php echo $this->html['pager']; ?>

<table id="problem-list-table" class="widget-table widget-table-hover mt10 mb10">
    <thead>
    <tr>
        <th width="8%" class="tc">专题ID</th>
        <th width="52%">专题名称</th>
        <th class="tc" width="5%">题数</th>
        <th width="20%">刷新时间</th>
        <th width="15%">创建者</th>
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
                <a href="/set_problem/?set-id=<?php echo $setInfo['id']; ?>"><?php echo $setInfo['title']; ?></a>
            </td>
            <td class="tc"><?php echo $setInfo['count']; ?></td>
            <td><?php echo date('Y-m-d H:i:s', $setInfo['refresh_at']); ?></td>
            <td>
                <a href="/user_my/?username=<?php echo $userInfo['username']; ?>"><?php echo OjCommonHelper::getColorName($userInfo); ?></a>
            </td>
        </tr>
    <?php   } ?>
    </tbody>
</table>



