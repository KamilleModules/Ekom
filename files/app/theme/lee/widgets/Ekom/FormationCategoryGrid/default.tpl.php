<?php

use Kamille\Utils\ThemeHelper\KamilleThemeHelper;
use Theme\Lee\ThisApp\Category\CategoryGrid\CategoryGridRenderer;

KamilleThemeHelper::css("page-category.css");

$cats = $v['cats'];



?>
<div class="category-grid-widget  window">
    <div class="flex-line">
        <?php CategoryGridRenderer::create()
            ->setImgName("formation_yoga")
            ->setTitle("YOGA")
            ->renderItem($cats['pilates']);
        ?>
        <?php CategoryGridRenderer::create()
            ->setImgName("formation_zen")
            ->setTitle("ZEN")
            ->renderItem($cats['zen']);
        ?>
        <?php CategoryGridRenderer::create()
            ->setImgName("formation_training")
            ->setTitle("TRAINING")
            ->renderItem($cats['training']);
        ?>
    </div>
</div>
