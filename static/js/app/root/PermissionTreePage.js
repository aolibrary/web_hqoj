define(function(require, exports, module) {

    var $ = require('jquery');
    require('jquery.jstree')($);
    require('jquery.form-validator')($);
    var layer = require('layer');
    var notice = require('notice');

    var makeJsTree = function() {

        var $tree = $('#permission-tree');
        var $searchBtn = $('#permission-tree-search');

        // 创建tree
        $tree.jstree({
            'core' : {
                'data' : {
                    'url' : '/permission_tree/ajaxGetJstreeJson/',
                    'dataType' : 'json'
                }
            },
            'plugins' : [ 'checkbox', 'search' ]
        });

        // 绑定查询
        var to = false;
        $searchBtn.keyup(function () {
            if(to) {
                clearTimeout(to);
            }
            to = setTimeout(function () {
                var v = $('#permission-tree-search').val();
                $('#permission-tree').jstree(true).search(v);
            }, 250);
        });

        // 执行第一次查询
        if ($searchBtn.val()) {
            to = setTimeout(function () {
                var v = $('#permission-tree-search').val();
                $('#permission-tree').jstree(true).search(v);
            }, 250);
        }

        // 展开选中
        $('#open-selected').click(function() {
            var ids = $tree.jstree().get_checked();
            $tree.jstree().open_node(ids);
        });

        // 收起选中
        $('#close-selected').click(function() {
            var ids = $tree.jstree().get_checked();
            $tree.jstree().close_node(ids);
        });

        // 删除选中
        $('#delete-selected').click(function() {

            var ids = $('#permission-tree').jstree().get_checked();

            if (ids.length <= 0) {
                return false;
            }

            layer.confirm('注意：删除前，请确保这些权限没有被使用。<br />确定要删除选定的权限？', function() {
                $.ajax({
                    url: '/permission_delete/ajaxDelete/',
                    type: 'post',
                    dataType: 'json',
                    data: {
                        'ids' : $.toJSON(ids)
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
                // layer.close(index);
            });
        });

    };

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

    $.validate({
        'form' : '#permission-tree-add-form'
    });

    exports.init = function() {

        makeJsTree();
        bindAddBtn();
    };
});
