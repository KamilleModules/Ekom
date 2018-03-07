<?php


use Kamille\Utils\ThemeHelper\KamilleThemeHelper;
use ListParams\ListBundle\ListBundleInterface;
use Module\ThisApp\Ekom\View\Customer\CardTemplateRenderer;
use Module\ThisApp\Ekom\View\InfoTemplateRenderer;
use Module\ThisApp\Ekom\View\PaginationTemplateRenderer;
use Module\ThisApp\Ekom\View\SortTemplateRenderer;
use Theme\LeeTheme;

KamilleThemeHelper::css("customer-all.css");
LeeTheme::useLib("featherlight");



?>
<div class="widget widget-customer-wishlist">
    <h1 class="bar-red center thin">MES FAVORIS</h1>

    <?php


    /**
     * @var $bundle ListBundleInterface
     */
    $bundle = $v['listBundle'];
    $items = $bundle->getListItems();
    $pagination = $bundle->getPaginationFrame();
    $sort = $bundle->getSortFrame();
    $info = $bundle->getInfoFrame();


    $renderer = CardTemplateRenderer::create();
    $sortRenderer = SortTemplateRenderer::create();
    $paginationRenderer = PaginationTemplateRenderer::create();
    $infoRenderer = InfoTemplateRenderer::create();

    ?>

    <div class="widget-product-card-list" id="widget-product-card-list">


        <?php echo $infoRenderer->render($info->getArray()); ?>
        <div class="sort-element">
            <?php echo $sortRenderer->render($sort->getArray()); ?>
        </div>
        <div class="cards-elements grid">
            <?php
            foreach ($items as $p) {
                $renderer->render($p);
            }
            ?>
        </div>
        <div class="pagination-element">
            <?php echo $paginationRenderer->render($pagination->getArray()); ?>
        </div>
    </div>


</div>