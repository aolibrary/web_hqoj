define(function(require, exports, module) {

    var $ = require('jquery');
    var layer = require('layer');
    var notice = require('notice');

    // 绑定添加管理员按钮
    var bindAddBtn = function() {

        $('.module-wrap2 input[name=add]').click(function(e) {
            e.preventDefault();
            $.layer({
                type: 2,
                title: '添加一个管理员',
                iframe: {src : '/manager_add/iframeAdd/' },
                area: ['600px' , '200px']
            });
        });
    };

    // 启用
    var bindEnable = function() {

        $('.module-wrap2 a[name=btn-enable]').click(function(e) {
            e.preventDefault();
            $.ajax({
                url: '/manager_list/ajaxEnable/',
                type: 'post',
                dataType: 'json',
                data: {
                    'manager-id': $(this).attr('manager-id')
                },
                success: function(result) {
                    if (0 === result.errorCode) {
                        location.reload();
                    } else {
                        notice('error', result.errorMessage);
                    }
                },
                error: function() {
                    notice('error', '服务器请求失败！');
                }
            });
        });
    };

    // 禁用
    var bindForbidden = function() {

        $('.module-wrap2 a[name=btn-forbidden]').click(function(e) {
            e.preventDefault();
            $.ajax({
                url: '/manager_list/ajaxForbid/',
                type: 'post',
                dataType: 'json',
                data: {
                    'manager-id': $(this).attr('manager-id')
                },
                success: function(result) {
                    if (0 === result.errorCode) {
                        location.reload();
                    } else {
                        notice('error', result.errorMessage);
                    }
                },
                error: function() {
                    notice('error', '服务器请求失败！');
                }
            });
        });
    };

    // 绑定添加权限按钮
    var bindAddPathBtn = function() {

        $('.module-wrap2 a[name=add-path]').click(function(e) {
            e.preventDefault();
            var managerId = $(this).attr('manager-id');
            $.layer({
                type: 2,
                title: '添加权限',
                iframe: {src : '/manager_path/iframeAdd/?manager-id=' + managerId},
                area: ['600px' , '200px']
            });
        });
    };

    // 绑定移除权限按钮
    var bindRemovePathBtn = function() {

        $('.module-wrap2 a[name=remove-path]').click(function(e) {
            e.preventDefault();
            var managerId = $(this).attr('manager-id');
            $.layer({
                type: 2,
                title: '移除权限',
                iframe: {src : '/manager_path/iframeRemove/?manager-id=' + managerId},
                area: ['600px' , '200px']
            });
        });
    };

    // 删除
    var bindDeleteBtn = function() {

        $('.module-wrap2 a[name=delete]').click(function(e) {
            e.preventDefault();
            var username = $(this).attr('username');
            var managerId = $(this).attr('manager-id');
            layer.confirm('确定删除管理员：' + username + '？', function() {
                $.ajax({
                    url: '/manager_list/ajaxDelete/',
                    type: 'post',
                    dataType: 'json',
                    data: {
                        'manager-id': managerId
                    },
                    success: function(result) {
                        if (0 === result.errorCode) {
                            location.reload();
                        } else {
                            notice('error', result.errorMessage);
                        }
                    },
                    error: function() {
                        notice('error', '服务器请求失败！');
                    }
                });
            });
        });
    };

    exports.init = function() {

        bindAddBtn();
        bindEnable();
        bindForbidden();
        bindAddPathBtn();
        bindRemovePathBtn();
        bindDeleteBtn();
    };
});
