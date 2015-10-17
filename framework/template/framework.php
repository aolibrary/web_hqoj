<!DOCTYPE html>
<html>
<head>
    <title><?php echo $this->htmlTitle; ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <?php foreach ($this->metaList as $name => $content) { ?>
        <meta name="<?php echo $name; ?>" content="<?php echo $content; ?>" />
    <?php } ?>
    <link type="favicon" rel="shortcut icon" href="//sta.hqoj.net/image/www/oj/favicon.ico" />
    <link type="text/css" href="//sta.hqoj.net/css/common/init.css" rel="stylesheet">
    <script src="//sta.hqoj.net/js/cgi/sea.js"></script>
</head>
<body>
<?php echo $this->frameworkContent; ?>
</body>
</html>

<script>
    seajs.use(['js/app/www/hm.js', 'jquery', 'jquery.lazyload', 'notice', 'jquery.cookie'], function(hm, $, fn1, notice, fn2) {

        fn1($); fn2($);
        $("img").lazyload({effect: "fadeIn"});

        var loadNotice = function() {
            var noticeArr = $.cookie('global_framework_notice');
            if (typeof noticeArr == 'undefined') {
                return false;
            }
            noticeArr = $.parseJSON(noticeArr);
            notice(noticeArr.type, noticeArr.message, noticeArr.timeout);
            $.cookie('global_framework_notice', '', {expires: -1, path: '/', domain: '.hqoj.net'});
        };
        loadNotice();
    });
</script>