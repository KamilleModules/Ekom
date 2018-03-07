<?php


use Kamille\Utils\ThemeHelper\KamilleThemeHelper;
use ListParams\ListBundle\ListBundleInterface;
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
" id="widget-product-card-listzzzz">
    <?php


    /**
     * @var $bundle ListBundleInterface
     */
    $bundle = $v['listBundle']; // ListParams\ListBundle\ListBundle
    $items = $bundle->getListItems();
    $pagination = $bundle->getPaginationFrame();
    $sort = $bundle->getSortFrame(); // ListParams\Controller\SortFrame
    $info = $bundle->getInfoFrame();


    //    $renderer = CardTemplateRenderer::create();
    $renderer = CarouselItemRenderer::create();
    $sortRenderer = ProductSortTemplateRenderer::create();
    $paginationRenderer = PaginationTemplateRenderer::create();
    $infoRenderer = InfoTemplateRenderer::create();


    ?>

    <?php if ($items): ?>
        <?php if (array_key_exists('title', $v)): ?>
            <h4 class="widget-title"><?php echo $v['title']; ?></h4>
        <?php endif; ?>

        <?php
        // echo $infoRenderer->render($info->getArray());
        ?>
        <div class="sort-element">
            <?php echo $sortRenderer->render($sort->getArray()); ?>
        </div>
        <div class="cards-elements grid product-boxes">
            <?php

            foreach ($items as $p) {
                $renderer->render($p);
            }
            ?>
        </div>
        <div class="pagination-element">
            <?php echo $paginationRenderer->render($pagination->getArray()); ?>
        </div>


    <?php else: ?>
        <div class="central-statement">
            Aucun élément trouvé pour cette recherche.
        </div>
    <?php endif; ?>
</div>

