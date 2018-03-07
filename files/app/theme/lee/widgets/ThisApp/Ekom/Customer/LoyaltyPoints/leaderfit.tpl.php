<?php


use Kamille\Utils\ThemeHelper\KamilleThemeHelper;
use ListParams\ListBundle\ListBundleInterface;
use Module\Ekom\Utils\EkomPhoneUtil;
use Module\ThisApp\Ekom\View\InfoTemplateRenderer;
use Module\ThisApp\Ekom\View\PaginationTemplateRenderer;
use Module\ThisApp\Ekom\View\ProductListItemRenderer;
use Module\ThisApp\Ekom\View\SimpleProductListItemRenderer;
use Module\ThisApp\Ekom\View\SortTemplateRenderer;
use Module\ThisApp\Ekom\View\User\LoyaltyPointsPageRenderer;
use Module\ThisApp\SokoForm\Renderer\ThisAppSokoTableFormRenderer;
use Theme\LeeTheme;

KamilleThemeHelper::css("customer-all.css");
LeeTheme::useLib("soko");


?>


<div
        id="widget-customer-account-loyalty-points"
        class="widget widget-customer-account-loyalty-points bionic-context"
>
    <?php echo LoyaltyPointsPageRenderer::render($v); ?>
</div>
<div class="bionic-marker" data-type="intent" data-value="loyalty-points"></div>
<script>
    jqueryComponent.ready(function () {
        var api = ekomApi.inst();
        var jContext = $("#widget-customer-account-loyalty-points");
        api.on('user.subscribeToLfPointsCatalogOvertureSuccess', function (data) {
            jContext.empty().append(data.loyaltyPageHtml);
        });
    });
</script>