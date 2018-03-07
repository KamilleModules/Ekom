(function($) {

    $.collapsibleBar = function(element, options) {

        var defaults = {
            foo: 'bar',
            onFoo: function() {}
        };

        var plugin = this;

        plugin.settings = {};

        var $element = $(element);

        plugin.init = function() {
            $element.on('click', function(e){
                var jTarget = $(e.target);
                if (jTarget.hasClass("collapse-trigger")) {
                    var jBar = jTarget.closest(".collapsible-bar");
                    var jItem = jBar.closest(".collapsible-parent");
                    if (jItem.hasClass("open")) {
//                        jContent.show();
                        jItem.removeClass("open");
                        jBar.removeClass("open");
                    }
                    else {
//                        jContent.hide();
                        jItem.addClass("open");
                        jBar.addClass("open");
                    }
                    return false;
                }
            });
        };

        plugin.init();

    };

    $.fn.collapsibleBar = function(options) {

        return this.each(function() {
            if (undefined == $(this).data('collapsibleBar')) {
                var plugin = new $.collapsibleBar(this, options);
                $(this).data('collapsibleBar', plugin);
            }
        });
    };

})(jQuery);