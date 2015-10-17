<style>
    .module-menu { width:190px; float:left; position:relative; }
    .module-menu .item .title { padding: 10px 20px; background-color: #ddd; font-weight: 700; }
    .module-menu .item li a { display: inline-block; padding: 0 20px; font-size: 12px; color: #333; border-right: 3px solid #fff; }
    .module-menu .item .selected,
    .module-menu .item li a:hover { background-color: #f5f5f5; border-right: 3px solid #C40000; }
    .module-menu .item span { display: inline-block; font-size: 12px; padding: 12px 0; width: 147px; border-top: 1px solid #f5f5f5; }
    .module-menu-content { float: left; width: 990px; margin: 0 0 0 20px; }
</style>


<?php if (!empty($this->backendMenuList)) { ?>
    <div class="module-menu">
        <?php   foreach ($this->backendMenuList as $menuInfo) { ?>
            <div class="item">
                <div class="title"><?php echo $menuInfo['title']; ?></div>
                <ul>
                    <?php
                    foreach ($menuInfo['menu'] as $menuItem) {
                        if (!empty($menuItem['hidden'])) {
                            continue;
                        }
                        $class = !empty($this->backendMenuInfo) && $this->backendMenuInfo['url'] == $menuItem['url'] ? 'class="selected"' : '';
                        ?>
                        <li><a <?php echo $class; ?> href="<?php echo $menuItem['url']; ?>"><span class="text"><?php echo $menuItem['title']; ?></span></a></li>
                    <?php   } ?>
                </ul>
            </div>
        <?php   } ?>
    </div>
    <div class="module-menu-content">
        <?php echo $this->backendContent; ?>
    </div>
<?php } else { ?>
    <?php echo $this->backendContent; ?>
<?php } ?>

