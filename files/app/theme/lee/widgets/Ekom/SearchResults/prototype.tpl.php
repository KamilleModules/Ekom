<?php


use Kamille\Utils\ThemeHelper\KamilleThemeHelper;
use Module\Ekom\Api\EkomApi;
use Theme\Lee\Ekom\Carousel\CarouselItemRenderer;
use Theme\LeeTheme;


LeeTheme::useLib("featherlight");
KamilleThemeHelper::css("product-carousel.css");
KamilleThemeHelper::css("widgets/widget-search-results.css");


$product = EkomApi::inst()->productLayer()->getProductBoxModelByProductId(1988);

$renderer = CarouselItemRenderer::create();
?>
<div class="widget widget-search-results window2">
    <div class="header">
        <div class="header-top">
            <div class="user-search">CROSS TRAIN<span class="deco-text">ING</span></div>
            <div class="info-result">
                <em>50 produits</em> correspondent à votre recherche "cross training"
            </div>
        </div>
        <div class="filter-bar">
            <form>
                Trier
                <select name="sort">
                    <option value="ee">Par défaut</option>
                </select>
            </form>
        </div>
    </div>
    <div class="results-list-container carousel-products">
        <div class="product-boxes poster">
            <?php for ($i = 1; $i <= 30; $i++): ?>
                <?php
                $renderer->render($product);
                ?>
            <?php endfor; ?>
        </div>
    </div>
</div>


<script>
    document.addEventListener("DOMContentLoaded", function (event) {
        $(document).ready(function () {

            var jContext = $('body');
            jContext.on('click', function (e) {
                var jTarget = $(e.target);
                if (jTarget.hasClass("product-preview-trigger")) {
                    var jCard = jTarget.closest(".product-box");
                    var pid = jCard.attr('data-pid');
                    window.leeOpenPreview(pid);
                    return false;
                }

            });


        });
    });
</script>

