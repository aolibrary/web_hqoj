<link rel="stylesheet" href="//sta.hqoj.net/js/plugin/kindeditor/themes/default/default.css" />
<link rel="stylesheet" href="//sta.hqoj.net/js/plugin/kindeditor/plugins/code/prettify.css" />

<div class="p10 bg-white" style="border: 1px solid #ddd;">
    <div class="tc p10" style="height: 85px; border-bottom: 1px dashed #eee;">
        <h3 class="f20 bg-gray mb10"><?php echo $this->docInfo['title']; ?></h3>
        <p class="mt10 fl">类别：<?php echo Arr::get($this->docInfo['category'], DocVars::$CATEGORY, '其他'); ?>&nbsp;<a href="/edit/?doc-id=<?php echo $this->docInfo['id']; ?>">【编辑文档】</a></p>
        <p class="mt10 fr">作者：<?php echo $this->userInfo['username']; ?></p>
    </div>
    <div class="p10"><?php echo $this->docInfo['content']; ?></div>
</div>

<script charset="utf-8" src="//sta.hqoj.net/js/plugin/kindeditor/plugins/code/prettify.js"></script>
<script>
    prettyPrint();
</script>