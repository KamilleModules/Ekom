/**
 * cloneTemplate
 * ===================
 * lingtalfi: 2017-05-30
 *
 * https://github.com/lingtalfi/cloneTemplate
 *
 *
 *
 *
 * This is a jquery mini-template system.
 *
 *
 *
 * Use case
 * ===========
 * You are in your js code.
 * You receive an array from an ajax service, and now
 * you want to this array needs to be transformed to a visual thing.
 *
 * Therefore, you created a template with some placeholders,
 * and the array contains the keys and values you want to replace
 * the placeholders with (the keys of the array being the placeholder names,
 * and the values of the array being the replacing values).
 *
 *
 * So, since your template should not be shown, you hid it inside a hidden div.
 * Now, you create a reference for it:
 *
 * var jTpl = $('.templates .mytemplate');
 *
 * Your array is called aItems;
 *
 * Now you are ready to inject aItems into your template,
 * you just miss one tool: and that would be cloneTemplate :)
 *
 *
 *
 * How to?
 * =============
 *
 * This section is divided in two parts.
 *
 * - How to call the plugin
 * - How to prepare the template
 *
 *
 *
 *
 * - How to call the plugin
 * --------------------------
 * Here is a concrete example snippet, and below is the same example in its simplest form.
 *
 *
 *
 * full example
 * ---------------------
 * Imagine we are doing an e-commerce website and we have a mini-cart widget.
 * The mini-cart widget is an icon, and when you hover it, a drop down list appears,
 * listing all the items in your cart.
 *
 *
 *
 * var jMiniCart = $('#mini-cart');
 * var jTpl = jMiniCart.find(".templates li:first"); // this template is hidden in my design
 * var jListContainer = jMiniCart.find(".mini-products-list");
 *
 * function updateByCartInfo(info) {  // this function is called after retrieving the cart info via ajax
 *
 *     var items = info["items"];
 *     jListContainer.empty();  // clean the current list
 *
 *     // and recreate a new one...
 *     for (var i in items) {
 *         var item = items[i];
 *         var jClone = $.fn.cloneTemplate(jTpl, item);
 *         jListContainer.append(jClone);
 *     }
 * }
 * ---------------------
 *
 *
 * simplest example
 * ---------------------
 * var variables = {"marie": "mary"};  // this dumb/demo map will simply replace marie by mary
 * var jClone = $.fn.cloneTemplate(jTpl, variables);
 *
 *
 *
 * - How to prepare the template
 * -------------------------------
 * The template, just use this syntax:
 *
 *
 * - {-myTag-}
 *
 * It is deliberately unusual, so that it can mix well with other syntax (like {myTag} for instance).
 *
 * If your placeholder is going to be inside an attribute,
 * or if for some other reason? you want to escape the usual html chars (like htmlspecialchars does, sort of),
 * prefix the placeholder name with the percent symbol (%), like this:
 *
 *
 * - {-%myTag-}
 *
 *
 * Enjoy!
 *
 *
 *
 *
 * About images?
 * ==================
 * 2017-06-08
 *
 * If your template contains images, you might be tempted to do something like this:
 *
 * <img src="{-image-}" width="120" height="100">
 *
 * Well, don't do that!
 * I did that, but then checking my logs I realized that the browser actually makes the request to the
 * non existing "{-image-}" uri.
 *
 * We obviously don't want that.
 * Unfortunately, there is no simple mechanism to get rid of that problem with html,
 * so instead I implemented a little "workaround" in cloneTemplate:
 *
 * just replace the src attribute with the data-src attribute, and cloneTemplate will magically handle the rest for you :)
 *
 * <img data-src="{-image-}" width="120" height="100">
 *
 *
 *
 *
 *
 *
 *
 *
 */
(function ($) {
    function escapeHtml(unsafe) {
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    var tplEngine = function (tpl, name, value) {

        var pattern = new RegExp("{-(%?)(" + name + ")-}");
        while (match = pattern.exec(tpl)) {
            if ('%' === match[1]) {
                value = escapeHtml(value);
            }
            tpl = tpl.replace(match[0], value)
        }
        return tpl;
    };

    $.fn.cloneTemplate = function (jTpl, vars) {

        var content = jTpl.prop("outerHTML");
        for (var name in vars) {
            var value = vars[name];
            content = tplEngine(content, name, value);
        }
        var jRet = $(content);

        jRet.find('img').each(function () {
            $(this).attr('src', $(this).attr("data-src"));
        });
        return jRet;
    };


    /**
     * I first tried this commented version below, to allow such syntax:
     *
     * var jClone = jTpl.templatize(jTpl, item);
     *
     * But I couldn't, so I gave up.
     * If someone knows how to do it, please tell me...
     */
    // $.fn.templatize = function (vars) {
    //
    //     return this.each(function () {
    //
    //         var zis = $(this);
    //         var content = $(this).prop("outerHTML");
    //         for (var name in vars) {
    //             var value = vars[name];
    //             content = tplEngine(content, name, value);
    //         }
    //         zis = $(this).replaceWith($(content));
    //     });
    // };

}(jQuery));