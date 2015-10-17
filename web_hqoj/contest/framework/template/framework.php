<link type="text/css" href="//sta.hqoj.net/css/www/contest/framework.page.css" rel="stylesheet">

<div class="module-nav">
    <div class="module-nav2">
        <?php if ($this->contestInfo['is_diy']) { ?>
            <?php if ($this->contestInfo['end_time'] < time()) { ?>
                <a class="item" href="http://www.hqoj.net/diy_list/?passed=1">回到OJ</a>
            <?php } else { ?>
                <a class="item" href="http://www.hqoj.net/diy_list/">回到OJ</a>
            <?php } ?>
        <?php } else { ?>
            <?php if ($this->contestInfo['end_time'] < time()) { ?>
                <a class="item" href="http://www.hqoj.net/contest_list/?passed=1">回到OJ</a>
            <?php } else { ?>
                <a class="item" href="http://www.hqoj.net/contest_list/">回到OJ</a>
            <?php } ?>
        <?php } ?>
        <a class="item <?php echo Url::getPath() == '/' ? 'item-hover' : ''; ?>" href="/?contest-id=<?php echo $this->contestInfo['id']; ?>">比赛</a>
        <a class="item <?php echo Url::getPath() == '/problem_list/' ? 'item-hover' : ''; ?>" href="/problem_list/?contest-id=<?php echo $this->contestInfo['id']; ?>">题目</a>
        <a class="item <?php echo Url::getPath() == '/rank_list/' ? 'item-hover' : ''; ?>" href="/rank_list/?contest-id=<?php echo $this->contestInfo['id']; ?>">排名</a>
        <a class="item <?php echo Url::getPath() == '/status_list/' ? 'item-hover' : ''; ?>" href="/status_list/?contest-id=<?php echo $this->contestInfo['id']; ?>">状态</a>
    </div>
</div>

<?php if (!empty($this->contestInfo['notice'])) { ?>
    <marquee scrollamount="3" style="margin: 5px 0 5px 0; color:red; font-weight: 700; font-size: 14px; display: block;">
        <?php echo $this->contestInfo['notice']; ?>
    </marquee>
<?php } ?>

<div class="module-wrap" style="padding-bottom: 50px;">
    <div class="module-wrap2">
        <?php echo $this->frameworkContent; ?>
        <div style="clear:both;"></div>
    </div>
</div>

