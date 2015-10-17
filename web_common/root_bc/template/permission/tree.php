<form class="widget-form widget-form-toolbar bg-gray mb10">
    <div class="fl">
        <label class="label">查找：</label>
        <input class="w300 input" id="permission-tree-search" type="text" value="<?php echo Request::getGET('search'); ?>"/>
    </div>
    <div class="fr">
        <input name="add" class="w120 btn btn-blue" type="button" value="添加权限" />
    </div>
    <div style="clear:both;"></div>
</form>

<form class="widget-form mb10" style="border:0;">
    <div class="fl">
        <input class="btn btn-small w80" type="button" id="open-selected" value="展开" />
        <input class="btn btn-small w80" type="button" id="close-selected" value="收起" />
    </div>
    <div class="fr">
        <input class="btn btn-small w100 btn-red" type="button" id="delete-selected" value="删除" />
    </div>
    <div style="clear:both;"></div>
</form>

<div id="permission-tree" class="demo">
</div>

<script>
    seajs.use(['js/app/root/PermissionTreePage.js'], function(oPage) {
        oPage.init();
    });
</script>