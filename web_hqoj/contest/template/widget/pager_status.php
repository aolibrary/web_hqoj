<?php

$renderMaxId = $this->renderMaxId; // int 当前statusList最大的id
$renderMinId = $this->renderMinId; // int 当前statusList最小的id

$prev = $renderMaxId+1;
$next = $renderMinId-1;

if ($next < 0) {
    $next = 0;
}

?>

<ul class="widget-pager">
    <li><a class="item item-lg" href="<?php echo Url::getCurrentUrl(array('min-id' => null, 'max-id' => null)); ?>">首页</a></li>
    <?php if (!empty($renderMinId)) { ?>
        <li><a class="item item-lg" href="<?php echo Url::getCurrentUrl(array('min-id' => $prev, 'max-id' => null)) ?>">上一页</a></li>
        <li><a class="item item-lg" href="<?php echo Url::getCurrentUrl(array('min-id' => null, 'max-id' => $next)) ?>">下一页</a></li>
    <?php } ?>
    <li><a class="item item-lg" href="<?php echo Url::getCurrentUrl(array('min-id' => 1, 'max-id' => null)) ?>">末页</a></li>
</ul>