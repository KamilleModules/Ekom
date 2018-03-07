jqueryComponent.ready(function () {


    var threshold = 699;
    var windowState = 'large';


    //----------------------------------------
    // NIN-SHADOW LOADER
    //----------------------------------------
    var jNinShadow = null;
    window.ninShadow = null;
    window.ninShadowHelper = {
        start: function () {
            window.ninShadowHelper.onRequestLoaderPrepare();
            window.ninShadowHelper.onRequestLoaderStart();
        },
        end: function () {
            window.ninShadowHelper.onRequestLoaderEnd();
        },
        onRequestLoaderPrepare: function (action, module) {
            if (null !== window.ninShadow) {
                jNinShadow = window.ninShadow;
            }
        },
        onRequestLoaderStart: function (action, module) {
            if (null === jNinShadow) {
                jNinShadow = $("#nin-shadow");
            }
            jNinShadow.addClass('active');
            setTimeout(function () {
                jNinShadow.addClass('active2');
            }, 10);
        },
        onRequestLoaderEnd: function (action, module) {
            jNinShadow.removeClass('active');
            jNinShadow.removeClass('active2');
            jNinShadow = null;
            window.ninShadow = null;
        }
    };

    window.ekomJsApi.prototype.hooks.onRequestLoaderPrepare = window.ninShadowHelper.onRequestLoaderPrepare;
    window.ekomJsApi.prototype.hooks.onRequestLoaderStart = window.ninShadowHelper.onRequestLoaderStart;
    window.ekomJsApi.prototype.hooks.onRequestLoaderEnd = window.ninShadowHelper.onRequestLoaderEnd;


//----------------------------------------
// TOP MENU - SEARCH BUTTON
//----------------------------------------
    var jTopBar = $('#site-topbar');

    // var jSearchCancel = $('#searchbar-cancel');
    var jSearchContainer = $('.searchbar-container', jTopBar);
    var jSearchResultsPanel = $('.search-results-panel', jSearchContainer);
    var jSearchInput = $('#searchbar-input');

    var jSearchCategorySection = null;
    var jSearchProductsSection = null;
    var jSearchTemplates = null;
    var jSearchProductTpl = null;
    var jSearchCategoryTpl = null;
    var searchMinChar = 3;


    function buildSearch() {
        jTopBar.addClass("growing");
        $(window).off('keydown.topmenuSearch').on('keydown.topmenuSearch', function (e) {
            if (27 == e.which) {
                unbuildSearch();
            }
        });


        window.freezeClickCallback = function (_jTarget) {
            var _jContainer = _jTarget.closest('.searchbar-container');
            if (0 === _jContainer.length) {
                unbuildSearch();
                return true;
            }
        };

        jTopBar.find('#searchbar-input').focus();

    }

    function unbuildSearch() {
        window.freezeClickCallback = null;
        setTimeout(function () {
            jSearchInput.val('');
        }, 10);
        jTopBar.removeClass("growing");
        $(window).off('keydown.topmenuSearch');
        jSearchResultsPanel.hide();

    }


    function refreshSearchPanel(data, query) {
        jSearchResultsPanel.show();
        //----------------------------------------
        // PRODUCTS
        //----------------------------------------
        var jUl = jSearchProductsSection.find('.items-list');
        jUl.empty();
        var jClone = null;
        var products = data.products;
        if ($.isEmptyObject(products)) {
            jSearchProductsSection.hide();
        }
        else {
            jSearchProductsSection.show();
        }
        for (var i in products) {
            var product = products[i];
            jClone = $.fn.cloneTemplate(jSearchProductTpl, product);
            jUl.append(jClone);
        }


        //----------------------------------------
        // CATS
        //----------------------------------------
        var jUl2 = jSearchCategorySection.find('.items-list-categories');
        jUl2.empty();
        var cats = data.categories;
        if ($.isEmptyObject(cats)) {
            jSearchCategorySection.hide();
        }
        else {
            jSearchCategorySection.show();
        }
        for (var i in cats) {
            var cat = cats[i];
            jClone = $.fn.cloneTemplate(jSearchCategoryTpl, cat);
            jUl2.append(jClone);
        }
    }


    jSearchInput.myAutoComplete({
        uriService: "/service/Ekom/ecp/api?action=search.product",
        renderData: function (data, query) {
            if (null !== data) {
                refreshSearchPanel(data, query);
            }
            else {
                var jUl = jSearchProductsSection.find('.items-list');
                jUl.empty();
                var jUl2 = jSearchCategorySection.find('.items-list-categories');
                jUl2.empty();
            }
        },
        minChars: searchMinChar,
        panel: jSearchResultsPanel,
        isEmptyResults: function (data) {
            return (
                true === $.fn.myAutoComplete.isEmpty(data.products) &&
                true === $.fn.myAutoComplete.isEmpty(data.categories)
            );
        },
        onLoaderStart: function () {
            window.ninShadowHelper.start();
        },
        onLoaderEnd: function () {
            window.ninShadowHelper.end();
        }
    });


//----------------------------------------
// TOP MENU - HAMBURGER MENU FOR SMALL DEVICES
//----------------------------------------
    var socialContainer = $("#topmenu-social-icons-container");
    var socialUl = $("#topmenu-social-icons-ul");
    var catalogLink = $("#topmenu-catalogue-link");
    var hamburgerContainer = $("#topmenu-hamburger-container");
    var hamburgerTrigger = $("#topmenu-hamburger-icon-trigger");


    function hamMenu() {
        windowState = 'small';
        hamburgerContainer.append(socialUl);
        var li = $('<li class="topmenu-removeme first"></li>');
        socialUl.prepend(li);
        li.append(catalogLink);

        var overlay = $('<div class="overlay"></div>');
        hamburgerContainer.append(overlay);
        overlay.off("click").on('click', function () {
            hamburgerContainer.removeClass("expand");
        });

        socialUl.find('a').off('click').on('click', function () {
            hamburgerContainer.removeClass("expand");
        });
    }

    function normalMenu() {
        hamburgerContainer.find('.overlay').remove();
        socialContainer.append(socialUl);
        socialUl.find('.topmenu-removeme').remove();
        socialContainer.before(catalogLink);
        windowState = 'large';
    }

    hamburgerTrigger.on("click", function (e) {
        e.preventDefault();
        $(this).parent().addClass('expand');
    });


    var sw = document.body.clientWidth;
    if (sw <= threshold) {
        hamMenu();
    } else {
        normalMenu();
    }


    $(window).resize(function () {
        //----------------------------------------
        // HAMBURGER
        //----------------------------------------
        var sw = $(window).outerWidth();
        if (sw <= threshold && windowState !== 'small') {
            hamMenu();
        }
        if (sw > threshold && windowState !== 'large') {
            normalMenu();
        }
    });


//----------------------------------------
// TOP MENU - MAIN MENU PANELS OPENING/CLOSING
//----------------------------------------
    var jPanels = $("#topmenu-panels");
    var jTopMenuCancelOverlay = $('.topmenu-panels-cancel-overlay');
    var jPanel = null;
    var jTrigger = null;
    var timeoutId;


    function removeMenu() {
        jTopMenuCancelOverlay.removeClass('active');
        if (jPanel) {
            jPanel.hide();
        }
    }


    $(".panel-trigger").hover(function () {
        var zis = $(this);
        if (!timeoutId) {
            timeoutId = window.setTimeout(function () {
                jTopMenuCancelOverlay.addClass('active');
                timeoutId = null;


                var id = zis.attr("data-id");

                if ('panel-events' !== id) {
                    jPanel = $("#topmenu-" + id);
                    jPanels.show();
                    jPanels.find(".panel").not(zis).hide();
                    jTrigger = zis;
                    jPanel.css({
                        display: 'flex'
                    });
                }
                else {
                    removeMenu();
                }


            }, 300);
        }

    }, function () {
        if (timeoutId) {
            window.clearTimeout(timeoutId);
            timeoutId = null;
        }
    });


    jTopMenuCancelOverlay.hover(function () {
        removeMenu();
    });


    //----------------------------------------
    // TOP MENU - MY ACCOUNT LINK
    //----------------------------------------
    // $('.my-account-link').hover(function(){
    //     $(this).find(".my-account-link-panel").show();
    //
    // }, function(){
    //     $(this).find(".my-account-link-panel").hide();
    // });


//----------------------------------------
// CART
//----------------------------------------
// $('#mini-cart').hover(function () {
//     $(this).addClass("open");
// }, function () {
//     $(this).removeClass("open");
// });


//----------------------------------------
// OUR SERVICES
//----------------------------------------
    $('#ourservices-slick').slick({
        autoplay: false,
        arrows: true,
        draggable: false,
        slidesToShow: 3,
        responsive: [
            {
                breakpoint: 900,
                settings: {
                    slidesToShow: 2,
                    autoplay: false
                }
            },
            {
                breakpoint: 600,
                settings: {
                    slidesToShow: 1,
                    autoplay: true
                }
            }
        ]
    });


//----------------------------------------
// SCROLL AFFIX
//----------------------------------------
    var threshold = $(window).height() * 0.2;
    var jAffix = $("#scroll-affix");
    var jContactAffix = $("#scroll-contact-affix");
    var jAffixContainer = jContactAffix.find(".container");
    var isVisible = false;


    var api = window.ekomApi.inst();

    $(window).scroll(api.utils.debounce(function () {
        var scroll = $(window).scrollTop();
        if (false === isVisible && scroll > threshold) {
            jAffix.fadeIn();
            isVisible = true;
        }
        else if (true === isVisible && scroll < threshold) {
            jAffix.fadeOut();
            isVisible = false;
        }


    }, 200));


    jAffix.on('click', function () {

        $('html, body').animate({
            scrollTop: 0
        }, 200);
    });


    jContactAffix.on('click', function () {
        if (false === jAffixContainer.hasClass("active")) {
            jAffixContainer.addClass("active");
        }
        else {
            jAffixContainer.removeClass("active");
        }

    });


    //----------------------------------------
    // PREVIEW BUTTON FUNCTION
    //----------------------------------------
    var jAjaxTarget = null;
    window.leeOpenPreview = function (productId) {
        if (null === jAjaxTarget) {
            jAjaxTarget = $('#ajax-box-preview-target');
        }
        var width = $(window).width();
        // jAjaxTarget.width(width - 100);


        var api = ekomApi.inst();
        api.on('productBox.collectAddToCartOptions', function (opt, jTarget) {
            if (jTarget.hasClass('ajaxian')) {

                var uri = null;
                if (jTarget.hasClass('add-to-estimate')) {
                    uri = "/service/EkomEstimate/html/api?action=productBoxPopupItemAdded";
                }
                else { // .add-to-cart-btn
                    uri = "/service/Ekom/html/api?action=productBoxPopupItemAdded";
                }

                opt.onSuccess = function (model, product_id, productDetails) {

                    api.once('ekom.slidingCart.cartItemAddedOptions', function (options) {
                        options.willOpen = false;
                    });


                    $.post(uri, {
                        id: product_id,
                        details: productDetails
                    }, function (s) {
                        var current = $.featherlight.current();
                        var jFeather = current.$content;
                        jFeather.empty();
                        jFeather.append(s);
                    });
                };
            }
        });


        var uri = "/service/Ekom/html/api?action=productBoxPopup&pid=" + productId;
        jAjaxTarget.empty();
        jAjaxTarget.append('<img src="/img/loader/ajax-loader.gif" alt="loader">');
        $.featherlight(jAjaxTarget, {
            afterOpen: function () {
                $.get(uri, function (s) {
                    var current = $.featherlight.current();
                    var jFeather = current.$content;

                    jFeather.empty();
                    jFeather.append(s);


                    /**
                     * Fixing a problem I had with firefox and featherlight
                     * Note: I only used firefox (don't know how it renders in chrome...)
                     */
                    jFeather.closest('.featherlight-content').css({
                        'marginLeft': 'auto',
                        'marginRight': 'auto'
                    });
                });
            }
        });

    };


    window.freezeClickCallback = null;

    function initItems() {


        $('body').on('click', function (e) {

            var jTarget = $(e.target);

            if (null !== window.freezeClickCallback) {
                if (true === window.freezeClickCallback(jTarget)) {
                    window.freezeClickCallback = null;
                    return false;
                }
            }

            //----------------------------------------
            // CARDS
            //----------------------------------------
            if (jTarget.hasClass("card-preview-trigger")) {


                var jCard = jTarget.closest(".card");
                var pid = jCard.attr('data-pid');
                window.leeOpenPreview(pid);
                return false;
            }
            //----------------------------------------
            // CAROUSEL ITEMS
            // SEARCH RESULTS
            //----------------------------------------
            // else if (jTarget.hasClass("product-preview-trigger")) {
            //     var jCard = jTarget.closest(".product-box");
            //     var pid = jCard.attr('data-pid');
            //     window.leeOpenPreview(pid);
            //     return false;
            // }
            else if (jTarget.hasClass("ekt-pc")) {
                var jBox = jTarget.closest('.product-box');
                var href = jBox.attr("data-href");
                window.location.href = href;
                return false;
            }
            //----------------------------------------
            // TOPBAR - LANG
            //----------------------------------------
            else if (jTarget.hasClass("lang-select-trigger")) {
                jTarget.closest('.lang-widget').addClass('active');
                window.freezeClickCallback = function (_jTarget) {
                    var _jContainer = _jTarget.closest('.lang-widget-list');
                    if (0 === _jContainer.length) {
                        jTopBar.find('.lang-widget').removeClass('active');
                        return true;
                    }
                };
                return false;
            }
            //----------------------------------------
            // TOPBAR - SEARCH
            //----------------------------------------
            else if (jTarget.hasClass("main-search-trigger")) {

                jSearchCategorySection = jSearchContainer.find('.section-categories');
                jSearchProductsSection = jSearchContainer.find('.section-products');
                jSearchTemplates = jSearchContainer.find('.templates');
                jSearchProductTpl = jSearchTemplates.find('.items-list .item');
                jSearchCategoryTpl = jSearchTemplates.find('.items-list-categories .item');


                buildSearch();


                return false;
            }
            /**
             * If you right click, the panel disappear, workaround this
             */
            else if (jTarget.hasClass("topsearch-input")) {
                if (jSearchInput.val().length >= searchMinChar) {
                    jSearchResultsPanel.show();
                }
                return false;
            }

        });

    }


    initItems();

    //----------------------------------------
    // SIMPLE SELECT
    //----------------------------------------
    if ("simpleselect" in $.fn) {
        $(".simpleselect-auto").simpleselect();
    }


    //----------------------------------------
    // EKOM API SUCCESS NOTIFS
    //----------------------------------------
    function htmlNotif(text, type) {
        var sClass = "notification-modal";
        if ("error" === type) {
            sClass += " error";
        }
        var s = '';
        s += '<div class="' + sClass + '">';
        s += '<div class="container">';
        s += '<div class="image"></div>';
        s += '<div class="text">' + text + '</div>';
        s += '<div class="container">';
        s += '</div>';
        return s;
    }

    window.ekomJsApi.prototype.hooks.onRequestSuccessMessage = function (msg) {
        $.featherlight(htmlNotif(msg));
    };
    window.ekomJsApi.prototype.hooks.onRequestPublicError = function (msg) {
        $.featherlight(htmlNotif(msg, "error"));
    };

    /**
     * fixing leaderfit big screen:
     */
    // if ($(window).width() <= 1280) {
    //     $("body").addClass("screen-1280-or-less");
    // }


});


