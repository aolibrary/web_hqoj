<link rel="stylesheet" href="//sta.hqoj.net/js/plugin/kindeditor/themes/default/default.css" />
<link rel="stylesheet" href="//sta.hqoj.net/js/plugin/kindeditor/plugins/code/prettify.css" />
<link type="text/css" href="//sta.hqoj.net/css/www/oj/doc_list.page.css" rel="stylesheet">

<div class="sidebar">
    <div class="nav" id="sidebar-fixed" >
        <div class="menu">
            <ul>
                <?php foreach ($this->docList as $docInfo) { ?>
                    <li>
                        <a <?php echo Request::getGET('doc-id') == $docInfo['id'] ? 'class="hover"' : ''; ?> href="<?php echo Url::make(Url::getCurrentUrl(), array('doc-id' => $docInfo['id'])); ?>">
                            <span class="text"><?php echo $docInfo['title']; ?></span>
                        </a>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </div>
</div>


<div class="main">
    <div class="main-content">
        <?php if (!empty($this->docInfo)) { ?>
            <?php echo $this->docInfo['content']; ?>
        <?php } else { ?>
            <div style="font-size: 24px; margin: 200px 0 400px 0; text-align: center;">
                <?php echo Arr::get(Request::getGET('category', 1), DocVars::$CATEGORY, '其他'); ?>
            </div>
        <?php }?>
    </div>
</div>

<script charset="utf-8" src="//sta.hqoj.net/js/plugin/kindeditor/plugins/code/prettify.js"></script>
<script>
    prettyPrint();

    seajs.use(['jquery'], function($) {

        var bindHeaderScroll = function() {
            var $sidebar = $('#sidebar-fixed');
            var top = $sidebar.offset().top;
            var height = $sidebar.height();
            window.onscroll = function() {
                if (height < $(window).height() && $(window).scrollTop() > top-10) {
                    $('#sidebar-fixed').addClass('fixed');
                } else {
                    $('#sidebar-fixed').removeClass('fixed');
                }
            };
        };
        bindHeaderScroll();
    });
</script>