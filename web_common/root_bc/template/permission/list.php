<form class="widget-form widget-form-toolbar bg-gray mb10">
    <div class="fl">
        <label class="label">权限：</label>
        <input class="input" name="keyword" type="text" value="<?php echo Request::getGET('keyword'); ?>" />
        <input class="btn w80" type="submit" value="查找" />
    </div>
    <input name="add" type="button" class="fr btn btn-blue w120" value="添加权限" />
    <div style="clear: both;"></div>
</form>

<?php echo $this->html['pager']; ?>

<table class="widget-table widget-table-hover mt10">
    <thead>
        <tr>
            <th width="10%">ID</th>
            <th width="25%">权限码</th>
            <th width="25%">描述</th>
            <th width="20%">添加时间</th>
            <th width="20%">操作</th>
        </tr>
    </thead>
    <tbody>
        <?php   foreach ($this->permissionList as $permissionInfo) { ?>
            <tr>
                <td><?php echo $permissionInfo['id']; ?></td>
                <td><?php echo $permissionInfo['code']; ?></td>
                <td><?php echo $permissionInfo['description']; ?></td>
                <td><?php echo date('Y-m-d H:i:s', $permissionInfo['create_time']); ?></td>
                <td><a name="permission-edit" permission-id="<?php echo $permissionInfo['id']; ?>">编辑</a></td>
            </tr>
        <?php   }   ?>
    </tbody>
</table>

<script>
    seajs.use(['js/app/root/PermissionListPage.js'], function(oPage) {
        oPage.init();
    });
</script>