(function ($) {
    if (typeof $.fn.hasAttr !== 'function') {
        $.fn.hasAttr = function (name) {
            return this.attr(name) !== undefined;
        };
    }
})(jQuery);