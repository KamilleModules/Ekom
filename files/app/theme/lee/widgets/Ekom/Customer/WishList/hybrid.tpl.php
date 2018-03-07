<?php


use Kamille\Utils\ThemeHelper\KamilleThemeHelper;
use ListParams\ListBundle\ListBundleInterface;
use Module\ThisApp\Ekom\View\Hybrid\Info\InfoHybridWidget;
use Module\ThisApp\Ekom\View\Hybrid\Slice\PaginationHybridWidget;
use Module\ThisApp\Ekom\View\Hybrid\Sort\ProductSortHybridWidget;
use Module\ThisApp\Ekom\View\InfoTemplateRenderer;
use Module\ThisApp\Ekom\View\PaginationTemplateRenderer;
use Module\ThisApp\Ekom\View\ProductSortTemplateRenderer;
use Theme\Lee\Ekom\Carousel\CarouselItemRenderer;
use Theme\Lee\Ekom\Customer\Wishlist\LeeCustomerWishListRenderer;
use Theme\LeeTheme;

//KamilleThemeHelper::css("customer-all.css");
LeeTheme::useLib("featherlight");
LeeTheme::useLib("simpleselect");
KamilleThemeHelper::css("product-carousel.css");
KamilleThemeHelper::css("product-card-list.css");
KamilleThemeHelper::css("widgets/widget-customer-wishlist.css");

?>
<div
        id="widget-customer-wishlist"
        class="widget-product-card-list carousel-products carousel-product-list
widget-customer-wishlist
">
    <?php echo LeeCustomerWishListRenderer::render($v); ?>
</div>

<div class="bionic-marker"
     data-type="intent"
     data-value="wishlist"

></div>
<script>
    jqueryComponent.ready(function () {
        var jContext = $('#widget-customer-wishlist');
        var api = ekomApi.inst();
        api.on('user.wishlist.updated', function (data) {
            jContext.empty().append(data.wishlistHtml);
            jContext.find(".simpleselect").simpleselect();
        });

    });
</script>