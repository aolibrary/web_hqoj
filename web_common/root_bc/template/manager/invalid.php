<table class="widget-table widget-table-hover mt10">
    <thead>
    <tr>
        <th width="40%">权限路径</th>
        <th width="30%">拥有者</th>
        <th width="30%">操作</th>
    </tr>
    </thead>
    <tbody>
    <?php   foreach ($this->pathList as $pathInfo) { ?>
        <tr>
            <td><p class="red"><?php echo $pathInfo['name']; ?></p></td>
            <td>
                <a href="/manager_list/?path=<?php echo $pathInfo['name']; ?>&include-path=<?php echo $pathInfo['name']; ?>">
                    <?php echo $pathInfo['count']; ?>
                </a>
            </td>
            <td>
                <p><a name="delete" path="<?php echo $pathInfo['name']; ?>" href="#">从所有管理员中移除</a></p>
            </td>
        </tr>
    <?php   } ?>
    </tbody>
</table>

<script>
    seajs.use(['js/app/root/InvalidListPage.js'], function(oPage) {
        oPage.init();
    });
</script>