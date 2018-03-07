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
use Theme\LeeTheme;

//KamilleThemeHelper::css("customer-all.css");
LeeTheme::useLib("featherlight");
KamilleThemeHelper::css("product-carousel.css");
KamilleThemeHelper::css("product-card-list.css");

?>
<div class="widget-product-card-list carousel-products carousel-product-list
" id="widget-product-card-list">
    <?php

    if (array_key_exists('listBundle', $v)) {
        $bundle = $v['listBundle'];
    } else {
        $bundle = $v['bundle'];
    }
    $items = $bundle['general']['items'];


    ?>

    <?php if ($items): ?>
        <?php if (array_key_exists('title', $v)): ?>
            <h4 class="widget-title"><?php echo $v['title']; ?></h4>
        <?php endif; ?>

        <?php if (false):
            // todo: some design if you want this feature
            ?>
            <?php if (array_key_exists("general", $bundle)): ?>
            <?php InfoHybridWidget::create()->render($bundle['general']); ?>
        <?php endif; ?>
        <?php endif; ?>


        <?php if (array_key_exists('sort', $bundle)): ?>
            <div class="sort-element">
                <?php ProductSortHybridWidget::create()->render($bundle); ?>
            </div>
        <?php endif; ?>


        <div class="cards-elements grid product-boxes">
            <?php
            $renderer = CarouselItemRenderer::create();
            foreach ($items as $p) {
                $renderer->render($p);
            }
            ?>
        </div>
        <?php if (array_key_exists("slice", $bundle)): ?>
            <div class="pagination-element">
                <?php PaginationHybridWidget::create()->render($bundle['slice']); ?>
            </div>

        <?php endif; ?>

    <?php else: ?>
        <div class="central-statement">
            Aucun élément trouvé pour cette recherche.
        </div>
    <?php endif; ?>
</div>

