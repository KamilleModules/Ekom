<?php

use Module\Ekom\Utils\E;
use Theme\LeeTheme;


?>
<li class="mini-cart" id="mini-cart">
    <?php

    $sClass = "lee-hidden";
    if ($v['totalQuantity'] > 0) {
        $sClass = "";
    }
    ?>
    <span class="panel-trigger w3-badge total-quantity <?php echo $sClass; ?>">{totalQuantity}</span>

    <span class="panel-trigger lee-icon action action-cart">Panier</span>
</li>


<div class="cd-panel from-right is-visible" id="mini-cart-content">
    <header class="cd-panel-header">
        <h1>Mon Panier (<span class="total-quantity">{totalQuantity}</span> produits)</h1>
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
                        <li data-id="<?php echo $item['product_id']; ?>" class="cart-item <?php echo $sFirst; ?>">

                            <div class="left">
                                <a href="<?php echo $item['uri_card_with_ref']; ?>">
                                    <img width="120" height="100" src="<?php echo $item['image']; ?>">
                                </a>
                            </div>
                            <div class="middle">
                                <a class="product-label"
                                   href="<?php echo $item['uri_card_with_ref']; ?>"><?php echo $item['label']; ?></a>
                                <span class="ref">Réf: <?php echo $item['ref']; ?></span>
                                <div class="attributes">
                                    <?php
                                    $labels = [];
                                    foreach ($item['attributes'] as $attr) {
                                        $labels[] = $attr['value'];
                                    }
                                    echo implode(' | ', $labels);
                                    ?>
                                </div>

                                <input class="quantity-input" type="number"
                                       min="1"
                                       max="<?php echo $item['stock_quantity']; ?>"
                                       value="<?php echo $item['quantity']; ?>">
                            </div>
                            <div class="right">
                                <a class="remove-item" href="#">Delete</a>
                                <div class="price"><?php echo $item['salePrice']; ?></div>

                            </div>
                        </li>
                    <?php endforeach; ?>
                </ol>

                <div class="bottom-part">
                    <table class="price-table">
                        <tr>
                            <td>TOTAL HT</td>
                            <td class="total-without-tax">{linesTotal}</td>
                        </tr>
                        <tr>
                            <td>TOTAL TTC</td>
                            <td class="total-with-tax">{linesTotalWithTax}</td>
                        </tr>
                    </table>

                    <div>
                        (Estimation des frais de port à l'étape suivante)
                    </div>


                    <div class="clearer bottom-actions">
                        <button
                                onclick="window.location.href='<?php echo E::link("Ekom_cart"); ?>'; return false;"
                                type="button" class="button btn-inline lee-button red-button">TERMINER MA COMMANDE
                        </button>
                        <button
                                onclick="window.location.href='<?php echo E::link("Ekom_cart"); ?>'; return false;"
                                type="button" class="button btn-inline lee-button black-button">TRANSFORMER EN DEVIS
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

                    var jTar = $(e.target);
//                    console.log(jTar);


                    if ($(this).val() < 1) {
                        $(this).val('1');
                    }

//                    if ($(this).val() < 1) {
//                        $(this).val('1');
//                    }


                    var productId = $(this).closest('.cart-item').attr('data-id');
                    var newQty = $(this).val();
                    api.cart.updateItemQuantity(productId, newQty);


                }, <?php echo LeeTheme::get("qtyInputDebounceTime", 250); ?>));
            }


            //close the lateral panel
            jPanel.on('click', function (event) {
                if ($(event.target).is('.cd-panel') || $(event.target).is('.cd-panel-close')) {
                    closePanel();
                    event.preventDefault();
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
                        values.push(item.attributes[j]["value"]);
                    }

                    var sAttr = values.join(' | ');
                    item.attributes = sAttr;


                    var jClone = $.fn.cloneTemplate(jTpl, item);
                    jListContainer.append(jClone);
                    c++;
                }

                jMiniCart.find(".total-without-tax").html(info['linesTotal']);
                jMiniCart.find(".total-with-tax").html(info['linesTotalWithTax']);
                jMiniCart.find(".total-quantity").html(info['totalQuantity']);

                var jTotalQty = jMiniCartIcon.find(".total-quantity");
                jTotalQty.html(info['totalQuantity']);

                if (0 == info['totalQuantity']) {
                    jTotalQty.addClass('lee-hidden');
                }
                else {
                    jTotalQty.removeClass('lee-hidden');
                }

                listenToQuantiyChanges();


            }


            api.on('cart.updated', function (cartInfo) {
                updateByCartInfo(cartInfo);
            });
            api.on('cart.itemAdded', function (cartInfo) {
                openPanel();
            });


            jMiniCart.on('click', function (e) {
                var jTarget = $(e.target);
                if (jTarget.hasClass('remove-item')) {
                    var productId = jTarget.closest('.cart-item').attr('data-id');
                    api.cart.removeItem(productId);
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





