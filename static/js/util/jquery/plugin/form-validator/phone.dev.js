/**
 * form验证手机号
 */
define(function (require, exports, module) { return function (jQuery) {
(function($) {
    
    $.formUtils.addValidator({
        name : 'phone',
        validatorFunction : function(value, $el, config, language, $form) {
            if (value.match(/^1\d{10}$/)) return true;
            return false;
        },
        errorMessage : '请输入正确的手机号码',
        errorMessageKey: 'badPhone'
    });
    
})(jQuery);
};});