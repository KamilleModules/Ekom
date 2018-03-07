<?php


use Kamille\Utils\ThemeHelper\KamilleThemeHelper;
use ListParams\ListBundle\ListBundleInterface;
use Module\EkomEstimate\View\Customer\EstimateTemplateRenderer;
use Module\ThisApp\Ekom\View\InfoTemplateRenderer;
use Module\ThisApp\Ekom\View\PaginationTemplateRenderer;
use Module\ThisApp\Ekom\View\SortTemplateRenderer;

KamilleThemeHelper::css("customer-all.css");




/**
 * @var $bundle ListBundleInterface
 */
$bundle = $v['listBundle'];
$items = $bundle->getListItems();
$pagination = $bundle->getPaginationFrame();
$sort = $bundle->getSortFrame();
$info = $bundle->getInfoFrame();


$renderer = EstimateTemplateRenderer::create();
$sortRenderer = SortTemplateRenderer::create();
$paginationRenderer = PaginationTemplateRenderer::create();
$infoRenderer = InfoTemplateRenderer::create();


?>
<div class="widget widget-user-estimates">
    <h1 class="bar-red center main-title">MES DEVIS</h1>

    <?php echo $infoRenderer->render($info->getArray()); ?>
    <div class="sort-element">
        <?php echo $sortRenderer->render($sort->getArray()); ?>
    </div>
    <div class="invoice-elements grid">
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