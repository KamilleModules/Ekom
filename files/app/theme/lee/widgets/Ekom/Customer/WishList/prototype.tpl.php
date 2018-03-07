<div class="my-account"><div class="my-wishlist">
        <div class="page-title title-buttons">
            <h1>Wishlist</h1>
        </div>

        <form id="wishlist-view-form" action="http://ultimo.infortis-themes.com/demo/default/wishlist/index/update/wishlist_id/3/" method="post">
            <fieldset>
                <p class="wishlist-empty">You have no items in your wishlist.</p>
                <div class="buttons-set buttons-set2">



                </div>
            </fieldset>
        </form>

        <form id="wishlist-allcart-form" action="http://ultimo.infortis-themes.com/demo/default/wishlist/index/allcart/" method="post">
            <input name="form_key" type="hidden" value="6VaIgSGcM0c2OBhQ" />
            <div class="no-display">
                <input type="hidden" name="wishlist_id" id="wishlist_id" value="3" />
                <input type="hidden" name="qty" id="qty" value="" />
            </div>
        </form>

        <script type="text/javascript">
            //<![CDATA[
            var wishlistForm = new Validation($('wishlist-view-form'));
            var wishlistAllCartForm = new Validation($('wishlist-allcart-form'));

            function calculateQty() {
                var itemQtys = new Array();
                $$('#wishlist-view-form .qty').each(
                    function (input, index) {
                        var idxStr = input.name;
                        var idx = idxStr.replace( /[^\d.]/g, '' );
                        itemQtys[idx] = input.value;
                    }
                );

                $$('#qty')[0].value = JSON.stringify(itemQtys);
            }

            function addAllWItemsToCart() {
                calculateQty();
                wishlistAllCartForm.form.submit();
            }
            //]]>
        </script>
    </div>
    <div class="buttons-set">
        <p class="back-link"><a href="http://ultimo.infortis-themes.com/demo/default/customer/account/"><small>&laquo; </small>Back</a></p>
    </div></div>