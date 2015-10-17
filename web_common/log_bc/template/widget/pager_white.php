<?php

$renderAllCount = $this->renderAllCount; // int 记录总数
$renderPageSize = $this->renderPageSize; // int 每页记录数，用来计算最大页数
$renderRadius   = $this->renderRadius;   // int 半径

$currentPage = (int) Request::getGET('page', 1);

$lastPage   = intval($renderAllCount / $renderPageSize + (($renderAllCount % $renderPageSize) ? 1 : 0));
$lastPage   = $lastPage > 0 ? $lastPage : 0;
$firstPage  = $lastPage > 0 ? 1 : 0;
$prePage    = $lastPage > 0 ? $currentPage-1 : 0;
$nextPage   = $lastPage > 0 ? $currentPage+1 : 0;

$firstUrl = ($firstPage && $currentPage!=1) ? Url::getCurrentUrl(array('page' => 1)) : '#';
$lastUrl  = ($lastPage && $currentPage!=$lastPage) ? Url::getCurrentUrl(array('page' => $lastPage)) : '#';
$preUrl   = ($prePage && $prePage>=1) ? Url::getCurrentUrl(array('page' => $currentPage - 1)) : '#';
$nextUrl  = ($nextPage && $nextPage<=$lastPage) ? Url::getCurrentUrl(array('page' => $currentPage + 1)) : '#';

$length = min($lastPage, 2*$renderRadius+1);
$beginPage = max(1, $currentPage - $renderRadius);
if ($beginPage + 2*$renderRadius >= $lastPage) {
    $beginPage = max(1, $lastPage - 2*$renderRadius);
}

?>

<ul class="widget-pager widget-pager-white">
    <li><a class="item item-lg" name="pager" value="<?php echo $firstPage; ?>" href="<?php echo $firstUrl; ?>">首页</a></li><li><a class="item item-lg" name="pager" value="<?php echo $prePage; ?>" href="<?php echo $preUrl; ?>">上一页</a></li><?php
    if ($lastPage) {
        for ($i = $beginPage; $i < $beginPage+$length; $i ++) {
            $isSelected = ($currentPage == $i) ? 'selected' : '';
            $url = ($currentPage == $i) ? '#' : Url::getCurrentUrl(array('page' => $i));
            echo sprintf('<li><a class="item %s" name="pager" value="%s" href="%s">%s</a></li>', $isSelected, $i, $url, $i);
        }
    }
    ?><li><a class="item item-lg" name="pager" value="<?php echo $nextPage; ?>" href="<?php echo $nextUrl; ?>">下一页</a></li><li><a class="item item-lg" name="pager" value="<?php echo $lastPage; ?>" href="<?php echo $lastUrl; ?>">末页</a></li>
</ul>