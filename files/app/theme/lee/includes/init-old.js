$(document).ready(function () {


    var threshold = 699;
    var windowState = 'large';


//----------------------------------------
// TOP MENU - SEARCH BUTTON
//----------------------------------------
    var jTopBar = $('#site-topbar');
    var jSearchCancel = $('#searchbar-cancel');
    var jSearchInput = $('#searchbar-input');
    var jSearchTriggerSmall = $('#topmenu-searchtrigger-small');

    function searchMode() {
        jTopBar.addClass("growing");

        $(window).off('keydown.topmenuSearch').on('keydown.topmenuSearch', function (e) {
            if (27 == e.which) {
                searchModeOff();
            }
        });

        setTimeout(function () {

            jTopBar.addClass("searching");
            jSearchInput.focus();
            $(window).off('click.topmenuSearch').on('click.topmenuSearch', function () {
                if ('large' === windowState) {
                    searchModeOff();
                }
            });
        }, 1);
    }

    function searchModeOff() {
        jTopBar.removeClass("searching growing");
    }

    jSearchTriggerSmall.on('click', function () {
        searchMode();
        return false;
    });
    jSearchInput.on('click', function () {
        searchMode();
        return false;
    });
    jSearchCancel.on('click', function () {
        searchModeOff();
        return false;
    });


    jSearchInput.myAutoComplete({
        boris: "vian",
        minChars: 9
    });

    jSearchInput.autocomplete({
        serviceUrl: '/service/Ekom/json/api?action=product-search',
        onSelect: function (suggestion) {
            window.location.href = suggestion.data;
        },
        // onSearchComplete: function(query, suggestions){
        //     console.log("doo");
        //     console.log(query);
        //     console.log(suggestions);
        // },
        beforeRender: function (container, suggestions) {

            var jContainer = $(container);
            console.log("pou");
            console.log(container);
            console.log(suggestions);
            console.log(jContainer);
            for (var i in suggestions) {

                var suggestion = suggestions[i];
                var jDiv = $("<div>");
                jDiv.html(suggestion.pokemon);
                jContainer.find('.autocomplete-suggestion:eq(' + i + ')').append(jDiv);
            }
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
    var jPanel = null;
    var jTrigger = null;

    function reallyLeaveMe() {
        if (null !== jPanel) {
            setTimeout(function () {

                if (null !== jPanel && jPanel.filter(":hover").length > 0) {
                    jPanel.off('mouseleave').on('mouseleave', function () {
                        reallyLeaveMe();
                    });
                }
                else if (null !== jTrigger && true === jTrigger.filter(":hover").length > 0) {
                    jTrigger.off('mouseleave').on('mouseleave', function () {
                        reallyLeaveMe();
                    });
                }
                else {
                    jPanels.hide();
                    if (null !== jPanel) {
                        jPanel.hide();
                    }
                }
            }, 10);
        }
    }

    $(".panel-trigger").hover(function () {

        var id = $(this).attr("data-id");
        jPanel = $("#topmenu-" + id);
        if (jPanel.length > 0) {
            jPanels.show();
            jPanels.find(".panel").not($(this)).hide();
            jTrigger = $(this);
            jPanel.show();
        }
        else {
            jPanel = null;
        }
    }, function () {
        reallyLeaveMe();
    });


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


});