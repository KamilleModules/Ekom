<?php

use Kamille\Mvc\HtmlPageHelper\HtmlPageHelper;
use Kamille\Utils\ThemeHelper\KamilleThemeHelper;
use Module\Ekom\Utils\E;
use Theme\Lee\Ekom\MiniCart\CartItemRenderer;
use Theme\LeeTheme;


KamilleThemeHelper::css("ekom-card-combination/sliding-mini-cart.css");
KamilleThemeHelper::css("mini-cart-sliding.css");
KamilleThemeHelper::loadJsInitFile($v);
//HtmlPageHelper::addBodyEndSnippet();



$cartItemRenderer = CartItemRenderer::create();


?>
<li class="mini-cart mini-cart-icon" id="mini-cart">
    <?php

    $sClass = "lee-hidden";
    if ($v['cartTotalQuantity'] > 0) {
        $sClass = "";
    }

    ?>
    <div class="numberCircle panel-trigger <?php echo $sClass; ?>"><span class="panel-trigger total-quantity-mini">{cartTotalQuantity}</span>
    </div>

    <span class="panel-trigger lee-icon action action-cart">Panier</span>
</li>


<div class="cd-panel from-right" id="mini-cart-content">
    <header class="cd-panel-header">
        <h1>Mon Panier (<span class="total-quantity">{cartTotalQuantity}</span> produits)</h1>
        <a href="#0" class="cd-panel-close">Close</a>
    </header>

    <div class="cd-panel-container">
        <div class="cd-panel-content">

            <!--  -->

            <div class="mini-cart-content dropdown-content left-hand skip-content skip-content--style block-cart block">

                <ol id="cart-sidebar" class="mini-products-list clearer">

                    <?php
                    $c = 0;
                    $max = count($v['items']);
                    foreach ($v['items'] as $item):
                        $sFirst = (0 === $c) ? 'first' : '';
                        $c++;
                        $sOddEven = (1 === $c % 2) ? 'odd' : 'even';


                        ?>
                        <li data-token="<?php echo $item['cartToken']; ?>" class="cart-item <?php echo $sFirst; ?>">

                            <div class="left">
                                <a href="<?php echo $item['uriProductInstance']; ?>">
                                    <img width="120" height="100" src="<?php echo $item['imageThumb']; ?>">
                                </a>
                            </div>
                            <div class="middle">
                                <a class="product-label"
                                   href="<?php echo $item['uriProductInstance']; ?>"><?php echo $item['label']; ?></a>
                                <span class="ref">Réf: <?php echo $item['ref']; ?></span>
                                <?php if ($item['attributes']): ?>
                                    <div class="attributes">
                                        <?php
                                        $labels = [];
                                        foreach ($item['attributes'] as $attr) {
                                            $labels[] = $attr['value_label'];
                                        }
                                        echo implode(' | ', $labels);
                                        ?>
                                    </div>
                                <?php endif; ?>
                                <div class="attributes-after">

                                    <!-- start-add-on: EkomCardCombination module -->
                                    <?php if (array_key_exists('eccCombinationSummary', $item)): ?>
                                        <div class="ekom-card-combination-items">
                                            <?php
                                            foreach ($item['eccCombinationSummary'] as $cardLabel => $attr):
                                                ?>
                                                <div class="ekom-card-combination-item">
                                                    <span class="label"><?php echo $cardLabel; ?></span>
                                                    <div class="attributes">
                                                        <?php foreach ($attr as $attrName => $attrValue): ?>
                                                            <span class="attribute"><?php echo $attrValue; ?></span>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                    <!-- end-add-on: EkomCardCombination module -->


                                    <?php $cartItemRenderer->renderItem($item); ?>

                                </div>

                                <input class="quantity-input" type="number"
                                       min="1"
                                       max="<?php echo (-1 !== (int)$item['quantityStock']) ? $item['quantityStock'] : 10000000; ?>"
                                       value="<?php echo $item['quantityCart']; ?>">
                            </div>
                            <div class="right">
                                <a class="remove-item" href="#">Delete</a>
                                <div class="price t-price"><?php echo $item['priceLine']; ?></div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ol>

                <div class="bottom-part">
                    <table class="price-table">
                        <tr>
                            <td>TOTAL HT</td>
                            <td class="total-without-tax t-price">{priceLinesTotalWithoutTax}</td>
                        </tr>
                        <tr>
                            <td>TOTAL TTC</td>
                            <td class="total-with-tax t-price">{priceLinesTotal}</td>
                        </tr>
                    </table>

                    <div>
                        (Estimation des frais de port à l'étape suivante)
                    </div>


                    <div class="clearer bottom-actions">
                        <button
                                onclick="window.location.href='<?php echo E::link("Ekom_cart"); ?>'; return false;"
                                type="button" class="button btn-inline lee-red-button">TERMINER MA COMMANDE
                        </button>
                        <button
                                type="button" class="button btn-inline lee-black-button transform-to-estimate-trigger">
                            TRANSFORMER EN DEVIS
                        </button>

                        <?php
                        //
                        //                        <!--                        <button type="button" title="Proceed to Checkout"-->
                        //<!--                                class="button btn-checkout btn-inline " onclick="window.location.href='--><?php //echo E::link("Ekom_checkoutOnePage");
                        //'; return false;"><span><span>Proceed to Checkout</span></span></button>
                        ?>

                    </div>
                </div>
            </div>
            <div style="display: none;" class="templates">
                <ul>

                    <li data-token="{-cartToken-}" class="cart-item {-first-}">

                        <div class="left">
                            <a href="{-%uriProductInstance-}">
                                <img data-src="{-imageThumb-}" width="120" height="100">
                            </a>
                        </div>
                        <div class="middle">
                            <a class="product-label"
                               href="{-%uriProductInstance-}">{-label-}</a>
                            <span class="ref">Réf: {-ref-}</span>
                            <div class="attributes">{-attributes-}</div>


                            <div class="attributes-after"></div>

                            <input class="quantity-input" type="number"
                                   min="1"
                                   max="{-quantityStock-}"
                                   value="{-quantityCart-}">
                        </div>
                        <div class="right">
                            <a class="remove-item" href="{-remove_uri-}">Delete</a>
                            <div class="price t-price">{-priceLine-}</div>

                        </div>
                    </li>
                </ul>
            </div>


            <!--  -->
        </div>
    </div>
</div>


<script>
    document.addEventListener("DOMContentLoaded", function (event) {
        $(document).ready(function () {

            var jPanel = $('.cd-panel');
            var api = ekomApi.inst();

            function openPanel() {
                jPanel.addClass('is-visible');
            }

            function closePanel() {
                jPanel.removeClass('is-visible');
            }


            function listenToQuantiyChanges() {


                jMiniCart.find(".quantity-input").on('change keyup', api.utils.debounce(function (e) {
                    if ($(this).val() < 1) {
                        $(this).val('1');
                    }

                    var token = $(this).closest('.cart-item').attr('data-token');
                    var newQty = $(this).val();
                    api.cart.updateItemQuantity(token, newQty);


                }, <?php echo LeeTheme::get("qtyInputDebounceTime", 250); ?>));
            }


            //close the lateral panel
            jPanel.on('click', function (event) {


                var jTarget = $(event.target);
                if (jTarget.hasClass("transform-to-estimate-trigger")) {
                    api.ekomEstimateJsApi.cart.importEkomCart(function () {

                        var data = {};
                        api.trigger('ekom.onTransformToEstimateAfter', data);
                        if ('reload' in data) {
                            var reload = data.reload;
                            if (true === reload) {
                                window.location.reload();
                            }
                            else {
                                window.location.href = reload;
                            }
                        }
                        closePanel();
                    });
                    return false;
                }
                else {
                    if ($(event.target).is('.cd-panel') || $(event.target).is('.cd-panel-close')) {
                        closePanel();
                        event.preventDefault();
                    }
                }
            });


            var jMiniCartIcon = $('#mini-cart');
            var jMiniCart = $('#mini-cart-content');
            var jTpl = jMiniCart.find(".templates li:first");
            var jListContainer = jMiniCart.find(".mini-products-list");

            function updateByCartInfo(info) {


                var items = info["items"];

                jListContainer.empty();
                var c = 0;
                for (var i in items) {

                    var item = items[i];


                    item.first = "";
                    if (0 === c) {
                        item.first = "first";
                    }

                    var values = [];
                    for (var j in item.attributes) {
                        values.push(item.attributes[j]["value_label"]);
                    }

                    var sAttr = values.join(' | ');
                    item.attributes = sAttr;


                    var jClone = $.fn.cloneTemplate(jTpl, item);

                    if (0 === values.length) { // no attributes?
                        jClone.find('.attributes').remove();
                    }

                    jListContainer.append(jClone);
                    jClone.find('.attributes-after').empty();


                    if ("eccCombinationSummary" in item) {
                        var s = '<div class="ekom-card-combination-items">';
                        for (var cardLabel in item["eccCombinationSummary"]) {
                            var attr = item["eccCombinationSummary"][cardLabel];
                            s += '<div class="ekom-card-combination-item">';
                            s += '<span class="label">' + cardLabel + '</span>';

                            s += '<div class="attributes">';
                            for (var attrName in attr) {
                                var attrValue = attr[attrName];
                                s += '<span class="attribute">' + attrValue + '</span>';
                            }
                            s += '</div>';

                            s += '</div>';
                        }
                        s += '</div>';
                        jClone.find('.quantity-input').before(s);
                    }


                    if ('eventDetails' in item) {
                        var details = item['eventDetails'];
                        var type = details['type'];


                        var s = '<div class="ekom-events-items">';
                        if (
                            '1-jour' === type ||
                            '2-jours' === type
                        ) {
                            var key = item['productDetails']['major']['day'];
                            var label = details['options'][key]["label"];
                            s += '<div class="ekom-events-item">';
                            s += '<span class="label">Détails: </span>';
                            s += '<span class="value">' + label + '</span>';
                            s += '</div>';
                        }
                        else {

                            var selCourses = item['productDetails']['minor']['dy'];
                            var days = details['options']['days'];

                            for (var j in days) {
                                var day = days[j];
                                var courses = day['courses'];
                                for (var m in courses) {
                                    var course = courses[m];
                                    var idCourse = course['id'];
                                    if (idCourse in selCourses) {
                                        s += '<div class="ekom-events-item">';
                                        s += '<span class="label">' + course['label'] + ': ' + course['start_time'] + ' - ' + course['end_time'] + '</span>';
                                        s += '<span class="value"> ( ' + selCourses[idCourse] + ' places )</span>';
                                        s += '</div>';
                                    }
                                }
                            }
                        }
                        s += '</div>';
                        jClone.find('.attributes-after').append(s);

                    }


                    if ('trainingInfo' in item) {
                        var tInfo = item['trainingInfo'];
                        var s = '<div class="ekom-training-products-items">';

                        s += '<div class="attribute-list-item">';
                        s += '<span class="label">Ville: </span>';
                        s += '<span class="value">' + tInfo.selectedCityLabel + '</span>';
                        s += '</div>';

                        s += '<div class="attribute-list-item">';
                        s += '<span class="label">Jour: </span>';
                        s += '<span class="value">' + tInfo.selectedDateRangeLabel + '</span>';
                        s += '</div>';

                        s += '</div>';
                        jClone.find('.attributes-after').append(s);

                    }


                    if ('-1' == '' + item["stock_quantity"]) {
                        jClone.find('.quantity-input').attr('max', 10000000);
                    }


                    c++;
                }

                jMiniCart.find(".total-without-tax").html(info['linesTotal']);
                jMiniCart.find(".total-with-tax").html(info['linesTotalWithTax']);
                jMiniCart.find(".total-quantity").html(info['totalQuantity']);

                var jTotalQty = jMiniCartIcon.find(".total-quantity-mini");
                jTotalQty.html(info['totalQuantity']);


                if (0 === parseInt(info['totalQuantity'])) {
                    jTotalQty.parent().addClass('lee-hidden');
                }
                else {
                    jTotalQty.parent().removeClass('lee-hidden');
                }

                listenToQuantiyChanges();


            }


            api.on('cart.updated', function (cartInfo) {
                updateByCartInfo(cartInfo);
                if (0 === cartInfo.items.length) {
                    closePanel();
                }
            });
            api.on('cart.itemAdded', function (cartInfo) {
                var options = {
                    willOpen: true
                };
                api.trigger('ekom.slidingCart.cartItemAddedOptions', options);
                if (true === options.willOpen) {
                    openPanel();
                }
            });


            jMiniCart.on('click', function (e) {
                var jTarget = $(e.target);
                if (jTarget.hasClass('remove-item')) {
                    var token = jTarget.closest('.cart-item').attr('data-token');
                    api.cart.removeItem(token);
                    return false;
                }
            });

            jMiniCartIcon.on('click', function (e) {
                var jTarget = $(e.target);
                if (jTarget.hasClass('panel-trigger')) {
                    openPanel();
                    return false;
                }
            });
            listenToQuantiyChanges();


            $(document).keyup(function (e) {
                if (e.keyCode === 27) {
                    closePanel();
                }
            });

        });
    });
</script>





