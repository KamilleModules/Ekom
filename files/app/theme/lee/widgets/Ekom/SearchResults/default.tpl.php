<?php


use Kamille\Utils\ThemeHelper\KamilleThemeHelper;
use ListParams\ListBundle\ListBundleInterface;
use Module\Ekom\Api\EkomApi;
use Module\ThisApp\Ekom\View\SortTemplateRenderer;
use Theme\Lee\Ekom\Carousel\CarouselItemRenderer;
use Theme\LeeTheme;


LeeTheme::useLib("featherlight");
KamilleThemeHelper::css("product-carousel.css");
KamilleThemeHelper::css("widgets/widget-search-results.css");


/**
 * @var $bundle ListBundleInterface
 */
$bundle = $v['listBundle'];

$product = EkomApi::inst()->productLayer()->getProductBoxModelByProductId(1988);

$renderer = CarouselItemRenderer::create();
$sort = $bundle->getSortFrame();
$sortRenderer = SortTemplateRenderer::create();
$items = $bundle->getListItems();



if (mb_strlen($v['search']) > 3) {
    $searchMinus3 = substr($v['search'], 0, -3);
    $searchLast3 = substr($v['search'], -3);
} else {
    $searchMinus3 = $v['search'];
    $searchLast3 = "";
}


?>
<div class="widget widget-search-results window2">
    <div class="header">
        <div class="header-top">
            <div class="user-search"><?php echo $searchMinus3; ?><span
                        class="deco-text"><?php echo $searchLast3; ?></span></div>
            <div class="info-result">
                <em><?php echo $v['nbProducts']; ?> produits</em> correspondent à votre recherche
                "<?php echo $v['search']; ?>"
            </div>
        </div>
        <div class="sort-element filter-bar">
            <?php echo $sortRenderer->render($sort->getArray()); ?>
        </div>


    </div>
    <div class="results-list-container carousel-products">
        <div class="product-boxes poster">
            <?php
            foreach ($items as $p) {
                $renderer->render($p);
            }
            ?>
        </div>
    </div>
</div>
