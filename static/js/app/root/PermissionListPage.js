define(function(require, exports, module) {

    var $ = require('jquery');
    var layer = require('layer');

    // 绑定添加按钮
    var bindAddBtn = function() {
        $('.module-wrap2 input[name=add]').click(function(e) {
            e.preventDefault();
            $.layer({
                type: 2,
                title: '添加权限码',
                iframe: {src : '/permission_add/iframeAdd/' },
                area: ['700px' , '300px']
            });
        });
    };

    // 绑定编辑
    var bindEditBtn = function() {
        $('.module-wrap2 a[name=permission-edit]').click(function(e) {
            e.preventDefault();
            var url = '/permission_edit/iframeEdit/?permission-id=' + $(this).attr('permission-id');
            $.layer({
                type: 2,
                title: '修改权限码',
                iframe: {src : url },
                area: ['600px' , '200px']
            });
        })
    };

    exports.init = function() {

        bindAddBtn();
        bindEditBtn();
    };
});
