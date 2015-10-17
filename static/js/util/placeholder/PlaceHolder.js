define(function(require, exports, module) {

    var $ = require('jquery');

    exports.init = function() {
        var isPlaceholder = function (){  
            var input = document.createElement('input');
            return 'placeholder' in input;
        };
        // 不支持placeholder 用jquery来完成
        if (!isPlaceholder()) {
            //把input绑定事件 排除password框
            $("input").not("input[type='password']").each(function() {
                if($(this).val() == "" && $(this).attr("placeholder") != "") {
                    $(this).val($(this).attr("placeholder"));
                    $(this).addClass('placeholder');
                    $(this).focus(function() {
                        if($(this).val() == $(this).attr("placeholder")) {
                            $(this).val("");
                            $(this).removeClass('placeholder');
                        }
                    });
                    $(this).blur(function() {  
                        if($(this).val() == "") {
                            $(this).val($(this).attr("placeholder"));
                            $(this).addClass('placeholder');
                        }
                    });
                }
            });
            // 对password框的特殊处理 1.创建一个text框  2获取焦点和失去焦点的时候切换
            $("input[type=password]").each(function() {
                var pwdField = $(this);
                var pwdVal   = pwdField.attr('placeholder');
                var name = $(this).attr('name');
                pwdField.after('<input id="' + name + '" class="placeholder" type="text" value='+pwdVal+' autocomplete="off" />');
                var pwdPlaceholder = $('#' + name);
                pwdPlaceholder.show();
                pwdField.hide();
                pwdPlaceholder.focus(function() {
                    pwdPlaceholder.hide();
                    pwdField.show();
                    pwdField.focus();
                });
                pwdField.blur(function() {  
                    if(pwdField.val() == '') {
                        pwdPlaceholder.show();
                        pwdField.hide();
                    }
                });
            });
        }
    };
});
