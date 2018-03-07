<?php

use Module\Ekom\Utils\E;

?>
<li class="mini-cart" id="mini-cart">
    <?php

    $sClass = "lee-hidden";
    if ($v['totalQuantity'] > 0) {
        $sClass = "";
    }
    ?>
    <span class="w3-badge total-quantity <?php echo $sClass; ?>">{totalQuantity}</span>

    <span class="lee-icon action action-cart">Panier</span>


    <div class="mini-cart-content dropdown-content left-hand skip-content skip-content--style block-cart block">
        <div class="">


            <div class="block-subtitle">Recently added item(s)</div>


            <ol id="cart-sidebar" class="mini-products-list clearer">

                <?php
                $c = 0;
                $max = count($v['items']);
                foreach ($v['items'] as $item):
                    $c++;
                    $sLast = ($max === $c) ? 'last' : '';
                    $sOddEven = (1 === $c % 2) ? 'odd' : 'even';
                    ?>
                    <li data-id="<?php echo $item['product_id']; ?>"
                        class="item <?php echo $sOddEven; ?> <?php echo $sLast; ?>">
                        <a href="<?php echo $item['uri']; ?>"
                           title="<?php echo htmlspecialchars($item['label']); ?>" class="product-image"><img
                                    src="<?php echo $item['image']; ?>"
                                    alt="<?php echo htmlspecialchars($item['label']); ?>" width="50" height="50"></a>

                        <div class="product-details">
                            <a href="<?php echo $item['remove_uri']; ?>"
                               title="Remove This Item"
                               onclick="return confirm('Are you sure you would like to remove this item from the shopping cart?');"
                               class="btn-remove nice">Remove This Item</a>
                            <!--                            <a href="http://ultimo.infortis-themes.com/demo/default/checkout/cart/configure/id/584/"-->
                            <!--                               title="Edit item" class="btn-edit nice">Edit item</a>-->


                            <p class="product-name"><a href="http://ultimo.infortis-themes.com/demo/default/bag2.html"
                                                       class="nice"><?php echo $item['label']; ?></a></p>
                            <strong><?php echo $item['quantity']; ?></strong> x


                            <span class="price"><?php echo $item['displayPrice']; ?></span>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ol>


            <div class="subtotal">
                <span class="label">Cart Subtotal:</span> <span class="total-without-tax price">{linesTotalWithoutTax}</span>
                <br>
                <span class="incl-tax">(<span class="price total-with-tax">{linesTotalWithTax}</span> Incl. Tax)</span>
            </div>

            <div class="actions clearer">
                <button type="button" title="View all items in your shopping cart"
                        class="button btn-inline"
                        onclick="window.location.href='<?php echo E::link("Ekom_cart"); ?>'; return false;">
                    <span><span>View All</span></span></button>

                <button type="button" title="Proceed to Checkout"
                        class="button btn-checkout btn-inline "
                        onclick="window.location.href='<?php echo E::link("Ekom_checkoutOnePage"); ?>'; return false;">
                    <span><span>Proceed to Checkout</span></span></button>
            </div>

        </div> <!-- end: inner block -->
    </div>
    <div style="display: none;" class="templates">
        <ul>
            <li data-id="{-product_id-}"
                class="item {-evenOdd-} {-last-}">
                <a href="{-%uri-}"
                   title="{-%label-}" class="product-image"><img
                            data-src="{-image-}"
                            alt="{-%label-}" width="50" height="50"></a>

                <div class="product-details">
                    <a href="{-remove_uri-}"
                       title="Remove This Item"
                       onclick="return confirm('Are you sure you would like to remove this item from the shopping cart?');"
                       class="btn-remove nice">Remove This Item</a>
                    <!--                            <a href="http://ultimo.infortis-themes.com/demo/default/checkout/cart/configure/id/584/"-->
                    <!--                               title="Edit item" class="btn-edit nice">Edit item</a>-->


                    <p class="product-name"><a href="http://ultimo.infortis-themes.com/demo/default/bag2.html"
                                               class="nice">{-label-}</a></p>
                    <strong>{-quantity-}</strong> x


                    <span class="price">{-displayPrice-}</span>
                </div>
            </li>
        </ul>
    </div>
</li>


<script>
    document.addEventListener("DOMContentLoaded", function (event) {
        $(document).ready(function () {



            var jMiniCart = $('#mini-cart');
            var jTpl = jMiniCart.find(".templates li:first");
            var jListContainer = jMiniCart.find(".mini-products-list");

            function updateByCartInfo(info) {
                var items = info["items"];

                jListContainer.empty();
                var c = 0;
                var max = items.length;
                for (var i in items) {
                    c++;
                    var item = items[i];
                    if (1 === c % 2) {
                        item.evenOdd = "odd";
                    }
                    else {
                        item.evenOdd = "even";
                    }


                    item.last = "";
                    if (max === c) {
                        item.last = "last";
                    }
                    var jClone = $.fn.cloneTemplate(jTpl, item);
                    jListContainer.append(jClone);
                }

                jMiniCart.find(".total-without-tax").html(info['linesTotalWithoutTax']);
                jMiniCart.find(".total-with-tax").html(info['linesTotalWithTax']);
                var jTotalQty = jMiniCart.find(".total-quantity");
                jTotalQty.html(info['totalQuantity']);

                if (0 == info['totalQuantity']) {
                    jTotalQty.addClass('lee-hidden');
                }
                else {
                    jTotalQty.removeClass('lee-hidden');
                }
            }


            var api = ekomApi.inst();
            api.on('cart.updated', function (cartInfo) {
                updateByCartInfo(cartInfo);
            });


            jMiniCart.on('click', function (e) {
                var jTarget = $(e.target);
                if (jTarget.hasClass('btn-remove')) {
                    var productId = jTarget.closest('.item').attr('data-id');
                    api.cart.removeItem(productId);
                    return false;
                }
            });

        });
    });
</script>





