<form class="widget-form widget-form-toolbar bg-gray mb10">
    <div class="fl">
        <input placeholder="用户名 | 邮箱 | 手机" name="login-name" class="input w180" type="text" value="<?php echo Request::getGET('login-name', ''); ?>" />
        <input placeholder="完全拥有权限查询" name="path" class="input w180" type="text" value="<?php echo Request::getGET('path', ''); ?>" />
        <input placeholder="部分拥有权限查询" name="include-path" class="input w180" type="text" value="<?php echo Request::getGET('include-path', ''); ?>" />
        <input type="submit" class="btn w80" value="查找" />
    </div>
    <input name="add" type="button" class="fr btn btn-blue w120" value="添加管理员" />
    <div style="clear: both;"></div>
</form>

<?php echo $this->html['pager']; ?>

<table class="widget-table widget-table-hover mt10">
    <thead>
        <tr>
            <th width="8%">ID</th>
            <th width="30%">管理员信息</th>
            <th width="30%">权限</th>
            <th width="6%">状态</th>
            <th width="14%">创建时间</th>
            <th width="12%">操作</th>
        </tr>
    </thead>
    <tbody>
        <?php   foreach ($this->managerList as $managerInfo) {
                    $userInfo = $this->userList[$managerInfo['user_id']];
                    $paths    = $this->pathHash[$managerInfo['id']];
        ?>
        <tr>
            <td><?php echo $managerInfo['id']; ?></td>
            <td>
                <p>UserId：<?php echo $userInfo['id']; ?></p>
                <p>用户名：<?php echo $userInfo['username']; ?></p>
                <p>邮&nbsp;&nbsp;&nbsp;箱：<?php echo $userInfo['email']; ?></p>
                <p>手&nbsp;&nbsp;&nbsp;机：<?php echo $userInfo['telephone']; ?></p>
            </td>
            <td>
                <?php   if (!empty($paths)) { ?>
                    <?php   foreach ($paths as $path) { ?>
                        <?php   if ($this->invalidHash[$path]) { ?>
                            <p class="red"><?php echo $path; ?></p>
                        <?php   } else { ?>
                            <p><?php echo $path; ?></p>
                        <?php   } ?>
                    <?php   } ?>
                <?php   } ?>
            </td>
            <td>
                <?php   if ($managerInfo['forbidden']) { ?>
                    <span class="red">禁用</span>
                <?php   } else { ?>
                    <span class="green">正常</span>
                <?php   } ?>
            </td>
            <td><?php echo date('Y-m-d H:i:s', $managerInfo['create_time']); ?></td>
            <td>
                <?php   if ($managerInfo['forbidden']) { ?>
                    <p><a name="btn-enable" manager-id="<?php echo $managerInfo['id']; ?>" href="#">恢复</a></p>
                <?php   } else { ?>
                    <p><a name="btn-forbidden" manager-id="<?php echo $managerInfo['id']; ?>" href="#">禁用</a></p>
                <?php   } ?>
                <p>
                    <a name="add-path" manager-id="<?php echo $managerInfo['id']; ?>" href="#">添加</a> |
                    <a name="remove-path" manager-id="<?php echo $managerInfo['id']; ?>" href="#">移除权限</a>
                </p>
                <p><a name="delete" username="<?php echo $userInfo['username']; ?>" manager-id="<?php echo $managerInfo['id']; ?>" href="#">删除管理员</a></p>
            </td>
        </tr>
        <?php   } ?>
    </tbody>
</table>

<script>
    seajs.use(['js/app/root/ManagerListPage.js'], function(oPage) {
        oPage.init();
    });
</script>