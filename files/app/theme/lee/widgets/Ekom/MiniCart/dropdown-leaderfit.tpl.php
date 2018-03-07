<?php

use Kamille\Utils\ThemeHelper\KamilleThemeHelper;
use Module\Ekom\Utils\E;
use Theme\LeeTheme;


KamilleThemeHelper::css("ekom-card-combination/sliding-mini-cart.css");
KamilleThemeHelper::css("mini-cart-dropdown.css");


?>
<li class="mini-cart-icon mini-cart-trigger" id="mini-cart-icon">
    <?php

    $sClass = "lee-hidden";
    if ($v['totalQuantity'] > 0) {
        $sClass = "";
    }
    ?>
    <span class="mini-cart-panel-trigger w3-badge total-quantity <?php echo $sClass; ?>">{totalQuantity}</span>

    <span class="mini-cart-panel-trigger lee-icon action action-cart">Panier</span>
</li>


<div class="templates" style="display: none">
    <div id="mini-cart-panel" class="mini-cart-panel">


        <div class="header">
            <h1>MON PANIER <span class="smaller">(<span class="total-quantity">{totalQuantity}</span> produits)</span>
            </h1>
            <a href="#0" class="panel-close">X</a>
        </div>
        <div class="product-list">

            <?php
            $c = 0;
            $max = count($v['items']);
            foreach ($v['items'] as $item):
                $sFirst = (0 === $c) ? 'first' : '';
                $c++;
                $sOddEven = (1 === $c % 2) ? 'odd' : 'even';
                ?>
                <div data-id="<?php echo $item['product_id']; ?>" class="cart-item <?php echo $sFirst; ?>">

                    <div class="left">
                        <a href="<?php echo $item['uri_card_with_ref']; ?>">
                            <img width="120" height="100" src="<?php echo $item['image']; ?>">
                        </a>
                        <a class="remove-item" href="#">Supprimer</a>
                    </div>
                    <div class="middle">
                        <a class="product-label"
                           href="<?php echo $item['uri_card_with_ref']; ?>"><?php echo $item['label']; ?></a>
                        <span class="ref">Réf: <?php echo $item['ref']; ?></span>
                        <div class="info-line">


                            <?php if ($item['attributes']): ?>
                                <div class="attributes">
                                    <?php
                                    $labels = [];
                                    foreach ($item['attributes'] as $attr) {
                                        $labels[] = $attr['value'];
                                    }
                                    echo implode(' | ', $labels);
                                    ?>
                                </div>
                            <?php endif; ?>

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


                            <!--                        <span class="quantity-info">QTÉ: <span class="quantity">-->
                            <?php //echo $item['quantity'];
                            ?><!--</span></span>-->
                            <span class="quantity-info">QTÉ:
                            <input class="quantity-input" type="number"
                                   min="1"
                                   max="<?php echo (-1 !== (int)$item['stock_quantity']) ? $item['stock_quantity'] : 10000000; ?>"
                                   value="<?php echo $item['quantity']; ?>">
                        </span>

                        </div>
                    </div>
                    <div class="right">
                        <div class="price">
                            <?php echo $item['salePrice']; ?>
                            <abbr>
                                <?php echo (true === $v['isB2B']) ? 'HT' : 'TTC'; ?>
                            </abbr>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="bottom-part">
            <table class="price-table">
                <tr>
                    <td>TOTAL HT</td>
                    <td class="total-without-tax">{linesTotal}</td>
                </tr>
                <tr>
                    <td>TVA 20%</td>
                    <td class="tax-amount">{taxAmount}</td>
                </tr>
                <tr>
                    <td>TOTAL TTC</td>
                    <td class="total-with-tax">{linesTotalWithTax}</td>
                </tr>
            </table>

            <div class="small-text">
                (Estimation des frais de port à l'étape suivante)
                <br>
                Vous cumulez <b>60 points</b> sur cette commande
            </div>


            <div class="clearer bottom-actions">
                <button
                        onclick="window.location.href='<?php echo E::link("Ekom_cart"); ?>'; return false;"
                        type="button" class="button btn-inline lee-red-button">PASSER LA COMMANDE
                </button>
                <button
                        onclick="window.location.href='<?php echo E::link("Ekom_cart"); ?>'; return false;"
                        type="button" class="button btn-inline lee-black-button">TRANSFORMER EN DEVIS
                </button>
            </div>
        </div>
    </div>
</div>


<div style="display: none;" class="templates">
    <ul>

        <li data-id="{-product_id-}" class="cart-item {-first-}">

            <div class="left">
                <a href="{-%uri-}">
                    <img data-src="{-image-}" width="120" height="100">
                </a>
            </div>
            <div class="middle">
                <a class="product-label"
                   href="{-%uri-}">{-label-}</a>
                <span class="ref">Réf: {-ref-}</span>
                <div class="attributes">{-attributes-}</div>


                <input class="quantity-input" type="number"
                       min="1"
                       max="{-stock_quantity-}"
                       value="{-quantity-}">
            </div>
            <div class="right">
                <a class="remove-item" href="{-remove_uri-}">Delete</a>
                <div class="price">{-salePrice-}</div>

            </div>
        </li>
    </ul>
</div>


<script>
    document.addEventListener("DOMContentLoaded", function (event) {
        $(document).ready(function () {


            var jTopBar = $("#site-topbar");
            var jPanel = $('.cd-panel');
            var jMiniCartIcon = $('#mini-cart-icon');
            var jMiniCartPanel = $('#mini-cart-panel');
            var api = ekomApi.inst();


            jTopBar.after(jMiniCartPanel);


            function openPanel() {
                jMiniCartPanel.addClass('opened');
            }

            function closePanel() {
                jMiniCartPanel.removeClass('opened');
            }

            function panelIsOpen() {
                return jMiniCartPanel.hasClass('opened');
            }


            $(".mini-cart-trigger").on('click', function () {
                if (true === panelIsOpen()) {
                    closePanel();
                }
                else {
                    openPanel();
                }
                return false;
            });




            jMiniCartPanel.on('click', function (e) {
                var jTarget = $(e.target);
                if (jTarget.hasClass("panel-close")) {
                    closePanel();
                    return false;
                }
            });

//            //close the lateral panel
//            jPanel.on('click', function (event) {
//                if ($(event.target).is('.cd-panel') || $(event.target).is('.cd-panel-close')) {
//                    closePanel();
//                    event.preventDefault();
//                }
//            });
//
//
//            var jMiniCart = $('#mini-cart-content');
//            var jTpl = jMiniCart.find(".templates li:first");
//            var jListContainer = jMiniCart.find(".mini-products-list");
//
//            function updateByCartInfo(info) {
//
//                var items = info["items"];
//
//                jListContainer.empty();
//                var c = 0;
//                for (var i in items) {
//                    var item = items[i];
//                    item.first = "";
//                    if (0 === c) {
//                        item.first = "first";
//                    }
//
//                    var values = [];
//                    for (var j in item.attributes) {
//                        values.push(item.attributes[j]["value"]);
//                    }
//
//                    var sAttr = values.join(' | ');
//                    item.attributes = sAttr;
//
//
//                    var jClone = $.fn.cloneTemplate(jTpl, item);
//                    jListContainer.append(jClone);
//
//
//                    if ("eccCombinationSummary" in item) {
//                        var s = '<div class="ekom-card-combination-items">';
//                        for (var cardLabel in item["eccCombinationSummary"]) {
//                            var attr = item["eccCombinationSummary"][cardLabel];
//                            s += '<div class="ekom-card-combination-item">';
//                            s += '<span class="label">' + cardLabel + '</span>';
//
//                            s += '<div class="attributes">';
//                            for (var attrName in attr) {
//                                var attrValue = attr[attrName];
//                                s += '<span class="attribute">' + attrValue + '</span>';
//                            }
//                            s += '</div>';
//
//                            s += '</div>';
//                        }
//                        s += '</div>';
//                        jClone.find('.quantity-input').before(s);
//                    }
//
//                    if ('-1' == '' + item["stock_quantity"]) {
//                        jClone.find('.quantity-input').attr('max', 10000000);
//                    }
//
//
//                    c++;
//                }
//
//                jMiniCart.find(".total-without-tax").html(info['linesTotal']);
//                jMiniCart.find(".total-with-tax").html(info['linesTotalWithTax']);
//                jMiniCart.find(".total-quantity").html(info['totalQuantity']);
//
//                var jTotalQty = jMiniCartIcon.find(".total-quantity");
//                jTotalQty.html(info['totalQuantity']);
//
//                if (0 == info['totalQuantity']) {
//                    jTotalQty.addClass('lee-hidden');
//                }
//                else {
//                    jTotalQty.removeClass('lee-hidden');
//                }
//
//                listenToQuantiyChanges();
//
//
//            }
//
//
//            api.on('cart.updated', function (cartInfo) {
//                updateByCartInfo(cartInfo);
//            });
//            api.on('cart.itemAdded', function (cartInfo) {
//                openPanel();
//            });
//
//
//            jMiniCart.on('click', function (e) {
//                var jTarget = $(e.target);
//                if (jTarget.hasClass('remove-item')) {
//                    var productId = jTarget.closest('.cart-item').attr('data-id');
//                    api.cart.removeItem(productId);
//                    return false;
//                }
//            });
//
//            jMiniCartIcon.on('click', function (e) {
//                var jTarget = $(e.target);
//                if (jTarget.hasClass('panel-trigger')) {
//                    openPanel();
//                    return false;
//                }
//            });
//            listenToQuantiyChanges();
//
//
//            $(document).keyup(function (e) {
//                if (e.keyCode === 27) {
//                    closePanel();
//                }
//            });

        });
    });
</script>





