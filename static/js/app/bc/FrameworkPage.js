define(function(require, exports, module) {
    
    var $ = require('jquery');
    require('jquery.cookie')($);
    require('jquery.json')($);
    
    var notice = require('notice');
    
    var loadNotice = function() {
        var noticeArr = $.cookie('global_framework_notice');
        if (typeof noticeArr == 'undefined') {
            return false;
        }
        noticeArr = $.parseJSON(noticeArr);
        notice(noticeArr.type, noticeArr.message, noticeArr.timeout);
        $.cookie('global_framework_notice', '', {expires: -1, path: '/', domain: '.hqoj.net'});
    };
    
    var bindTabHover = function() {
        $('.module-header .item-3').hover(
            function() {
                $(this).find('.tab-title').addClass('title-hover');
                $(this).find('.menu').show();
            },
            function() {
                $(this).find('.menu').hide();
                $(this).find('.tab-title').removeClass('title-hover');
            }
        );
    };
    
    var bindUserHover = function() {
        $('.module-header .item-4').hover(
            function() {
                $(this).find('.tab-title').addClass('title-hover');
                $(this).find('.menu').show();
            },
            function() {
                $(this).find('.menu').hide();
                $(this).find('.tab-title').removeClass('title-hover');
            }
        );
    };
    
    exports.init = function() {
        
        loadNotice();
        bindTabHover();
        bindUserHover();
    };
});
