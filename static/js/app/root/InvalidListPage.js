define(function(require, exports, module) {

    var $ = require('jquery');
    var notice = require('notice');

    // 删除
    var bindDeleteBtn = function() {

        $('.module-wrap2 a[name=delete]').click(function(e) {
            e.preventDefault();
            var path = $(this).attr('path');
            $.ajax({
                url: '/manager_invalid/ajaxDelete/',
                type: 'post',
                dataType: 'json',
                data: {
                    'path': path
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

    exports.init = function() {

        bindDeleteBtn();
    };
});
