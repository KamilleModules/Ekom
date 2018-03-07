<?php


use Kamille\Utils\ThemeHelper\KamilleThemeHelper;
use ListParams\ListBundle\ListBundleInterface;

use Module\EkomEstimate\View\Customer\EstimateTemplateRenderer;
use Module\ThisApp\Ekom\View\Customer\InvoiceTemplateRenderer;
use Module\ThisApp\Ekom\View\Hybrid\Slice\PaginationHybridWidget;
use Module\ThisApp\Ekom\View\Hybrid\Sort\ProductSortHybridWidget;
use Module\ThisApp\Ekom\View\InfoTemplateRenderer;
use Module\ThisApp\Ekom\View\PaginationTemplateRenderer;
use Module\ThisApp\Ekom\View\SortTemplateRenderer;

KamilleThemeHelper::css("customer-all.css");


?>
<div class="widget widget-user-invoices">
    <h1 class="bar-red center thin">MES DEVIS</h1>
</div>
<?php


$bundle = $v['bundle'];
$items = $bundle['general']['items'];
$renderer = EstimateTemplateRenderer::create();

?>

<div class="widget-product-card-list" id="widget-product-card-list">

    <?php if ($items): ?>
        <?php if (array_key_exists('sort', $bundle)): ?>
            <div class="sort-element">
                <?php ProductSortHybridWidget::create()->render($bundle); ?>
            </div>
        <?php endif; ?>

        <div class="invoice-elements grid">
            <?php
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
        Aucun devis disponible pour l'instant.
    <?php endif; ?>
</div>



