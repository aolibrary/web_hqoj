<link type="text/css" href="http://sta.hqoj.net/css/bc/framework.page.css" rel="stylesheet">

<div class="module-header">
    <div class="module-header2">
        <div class="item-1">
            <a href="/main/"><img class="logo" src="//sta.hqoj.net/image/bc/logo.png" height="35px" /></a>
        </div>
        <div class="item-2">
            <a href="#"><?php echo $this->backendProjectInfo['title']; ?></a>
        </div>
        <div class="item-3">
            <a class="tab-title" href="#">切换后台</a>
            <ul class="menu">
                <?php foreach ($this->backendProjectList as $projectInfo) { ?>
                    <li><a href="<?php echo $projectInfo['url']; ?>"><?php echo $projectInfo['title']; ?></a></li>
                <?php } ?>
            </ul>
        </div>
        <div class="item-4">
            <a class="tab-title" href="#"><?php echo $this->loginUserInfo['username']; ?></a>
            <ul class="menu">
                <li><a href="//uc.hqoj.net/logout/?back-url=<?php echo Url::getCurrentUrl(); ?>">退出</a></li>
            </ul>
        </div>
    </div>
</div>

<div class="module-breadcrumb">
    <a href="<?php echo $this->backendProjectInfo['url']; ?>"><?php echo $this->backendProjectInfo['title']; ?></a>
    <?php if (!empty($this->backendMenuInfo)) { ?> &gt;
    <?php echo $this->backendMenuInfo['parent_title']; ?> &gt;
        <?php if (!empty($this->backendMenuInfo['hidden'])) { ?>
            <?php echo $this->backendMenuInfo['title'] ;?>
        <?php } else { ?>
            <a href="<?php $this->backendMenuInfo['url']; ?>"><?php echo $this->backendMenuInfo['title'] ;?></a>
        <?php } ?>
    <?php } ?>
</div>

<div class="module-wrap">
    <div class="module-wrap2">
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
                                <li><a <?php echo $class; ?> href="<?php echo $menuItem['url']; ?>"><?php echo $menuItem['title']; ?></a></li>
                            <?php   } ?>
                        </ul>
                    </div>
                <?php   } ?>
            </div>
            <div class="module-content">
                <?php echo $this->backendContent; ?>
            </div>
        <?php } else { ?>
            <?php echo $this->backendContent; ?>
        <?php } ?>
    </div>
</div>

<div class="module-footer">

</div>

<script>
    seajs.use(['js/app/bc/FrameworkPage.js'], function(oPage) {
        oPage.init();
    });
</script>
