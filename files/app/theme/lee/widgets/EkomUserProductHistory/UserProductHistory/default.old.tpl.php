<?php


use Kamille\Utils\ThemeHelper\KamilleThemeHelper;
use Module\ThisApp\Ekom\View\Customer\CardTemplateRenderer;
use Module\ThisApp\Ekom\View\ListSortBarTemplateRenderer;
use Module\ThisApp\Ekom\View\PaginationTemplateRenderer;

KamilleThemeHelper::css("customer-all.css");


?>
<div class="widget widget-user-product-history">
    <h1 class="bar-red left thin">MES ARTICLES CONSULTÉS</h1>
</div>


<?php




$listSortBarModel = $v['listSortBar'];
$renderer = CardTemplateRenderer::create();
$listSortBarRenderer = ListSortBarTemplateRenderer::create();
$paginationRenderer = PaginationTemplateRenderer::create();
$pagination = $v['pagination'];

?>

<div class="widget-product-card-list" id="widget-product-card-list">


    <div class="list-sort-bar-element">
        <?php echo $listSortBarRenderer->render($listSortBarModel); ?>
    </div>
    <div class="cards-elements grid">
        <?php
        foreach ($v['cards'] as $p) {
            $renderer->render($p);
        }
        ?>
    </div>
    <div class="pagination-element">
        <?php echo $paginationRenderer->render($pagination); ?>
    </div>
</div>




