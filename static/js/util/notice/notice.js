define(function (require, exports, module) {
    
    var $ = require('jquery');
    var css = require('js/util/notice/notice.style.css');
    
    var templates = {
        'error': '<div class="alert alert-error f14"><a href="#" class="close">×</a><strong></strong><%= message %></div>',
        'success': '<div class="alert alert-success f14"><a href="#" class="close">×</a><strong></strong><%= message %></div>',
        'warn': '<div class="alert f14"><a href="#" class="close">×</a><strong></strong><%= message %></div>',
        'info': '<div class="alert alert-info f14"><a href="#" class="close">×</a><strong></strong><%= message %></div>'
    };
    
    var $alerts = $('#global-js-notice');
    if (!$alerts.size()) {
        $alerts = $('<div id="global-js-notice"></div>');
        $alerts.appendTo('body');
    }
    
    var timeout = 3;
    
    var notice = function(type, message, t, cb) {
        
        var _ = require('js/util/underscore/underscore-min.js');
        var template = _.template(templates[type]);
        var $el = $(template({
            message: message
        }));
        
        $alerts.prepend($el);
        $el.on('click', '.close', function removeEl () {
            $el.fadeOut();
        });
        
        if (! t) {
            t = timeout;
        }
        
        setTimeout(function () {
            $el.fadeOut();
            
            // 执行回调
            typeof cb != 'undefined' && cb();
            
        }, t*1000);
        
        
    };
    
    module.exports = notice;
});
