<?php

use Kamille\Architecture\ApplicationParameters\ApplicationParameters;
use Kamille\Utils\ThemeHelper\KamilleThemeHelper;

use Module\ThisApp\Ekom\View\Customer\CardTemplateRenderer;
use Module\ThisApp\Ekom\View\ListSortBarTemplateRenderer;
use Module\ThisApp\Ekom\View\PaginationTemplateRenderer;
use Theme\LeeTheme;

KamilleThemeHelper::css("product-card-list.css");


LeeTheme::useLib("featherlight");


$wwwDir = ApplicationParameters::get("app_dir") . "/www";
$defaultImgUri = '/modules/Ekom/img/no-image.jpg';


$circleValues = $v['circleValues'];
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



