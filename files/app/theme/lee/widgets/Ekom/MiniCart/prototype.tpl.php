<?php

use Module\Ekom\Utils\E;

?><li class="mini-cart" id="mini-cart">
    <span class="lee-icon action action-cart">Panier</span>


    <div class="mini-cart-content dropdown-content left-hand skip-content skip-content--style block-cart block">
        <div class="">


            <div class="block-subtitle">Recently added item(s)</div>


            <ol id="cart-sidebar" class="mini-products-list clearer">
                <li class="item odd">
                    <a href="http://ultimo.infortis-themes.com/demo/default/bag2.html"
                       title="Metropolis Small Bag" class="product-image"><img
                            src="http://ultimo.infortis-themes.com/demo/media/catalog/product/cache/1/thumbnail/50x50/9df78eab33525d08d6e5fb8d27136e95/7/3/734198-0056_1_2.jpg"
                            alt="Metropolis Small Bag" width="50" height="50"></a>
                    <div class="product-details">
                        <a href="http://ultimo.infortis-themes.com/demo/default/checkout/cart/delete/id/584/form_key/MOHyGgOt1HRwbRVH/uenc/aHR0cDovL3VsdGltby5pbmZvcnRpcy10aGVtZXMuY29tL2RlbW8vZGVmYXVsdC9mYXNoaW9uL2JhZ3MtcHVyc2VzL2Rlc2lnbmVyLWJhZ3MvYmFnMi5odG1s/"
                           title="Remove This Item"
                           onclick="return confirm('Are you sure you would like to remove this item from the shopping cart?');"
                           class="btn-remove nice">Remove This Item</a>
                        <a href="http://ultimo.infortis-themes.com/demo/default/checkout/cart/configure/id/584/"
                           title="Edit item" class="btn-edit nice">Edit item</a>
                        <p class="product-name"><a
                                href="http://ultimo.infortis-themes.com/demo/default/bag2.html"
                                class="nice">Metropolis Small Bag</a></p>
                        <strong>1</strong> x


                        <span class="price">$55.95</span>


                    </div>
                </li>
                <li class="item last even">
                    <a href="http://ultimo.infortis-themes.com/demo/default/top8.html"
                       title="Simple Woman Top" class="product-image"><img
                            src="http://ultimo.infortis-themes.com/demo/media/catalog/product/cache/1/thumbnail/50x50/9df78eab33525d08d6e5fb8d27136e95/8/_/8_1.jpg"
                            alt="Simple Woman Top" width="50" height="50"></a>
                    <div class="product-details">
                        <a href="#" title="Remove This Item"
                           onclick="return confirm('Are you sure you would like to remove this item from the shopping cart?');"
                           class="btn-remove nice">Remove This Item</a>
                        <a href="http://ultimo.infortis-themes.com/demo/default/checkout/cart/configure/id/449/"
                           title="Edit item" class="btn-edit nice">Edit item</a>
                        <p class="product-name"><a
                                href="http://ultimo.infortis-themes.com/demo/default/top8.html"
                                class="nice">Simple Woman Top</a></p>
                        <strong>2</strong> x


                        <span class="price">$39.50</span>


                    </div>
                </li>
            </ol>


            <div class="subtotal">
                <span class="label">Cart Subtotal:</span> <span class="price">$239.50</span>
                <br>
                <span class="incl-tax">(<span class="price">$239.50</span> Incl. Tax)</span>
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
</li>