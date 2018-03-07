<?php

use Kamille\Utils\ThemeHelper\KamilleThemeHelper;
use Theme\Lee\ThisApp\Category\CategoryGrid\CategoryGridRenderer;

KamilleThemeHelper::css("page-category.css");

$cats = $v['cats'];


/**
 * images réalisées
 * - equipement_crosstraining
 * - equipement_haltero
 * - equipement_musculation
 * - vidéo
 * - equipement_amenagement
 * - equipement_aqua
 * - equipement_coachingsportif
 * - equipement_de_salle
 * - equipement_pilates
 */

?>
<div class="category-grid-widget  window">
    <div class="flex-line">
        <?php CategoryGridRenderer::create()
            ->setImgName("equipement_crosstraining")
            ->setTitle("CROSS<br>TRAINING")
            ->renderItem($cats['cross_training']);
        ?>
        <?php CategoryGridRenderer::create()
            ->setImgName("equipement_haltero")
            ->setTitle("HALTÉROPHILIE")
            ->renderItem($cats['halterophilie']);
        ?>
        <?php CategoryGridRenderer::create()
            ->setImgName("equipement_musculation")
            ->setTitle("MUSCULATION")
            ->renderItem($cats['musculation']);
        ?>
    </div>
    <div class="flex-line">
        <?php CategoryGridRenderer::create()
            ->setImgName("video1")
            ->setUri("https://www.youtube.com/watch?v=sx_WEHbuEos")
            ->setSpaceTaken(2)
            ->setVideoOverlay(true)
            ->renderItem($cats['soin_du_sportif']);
        ?>
        <?php CategoryGridRenderer::create()
            ->setImgName("equipement_soinduportif")
            ->setTitle("SOIN<br>DU SPORTIF")
            ->renderItem($cats['soin_du_sportif']);
        ?>
    </div>
    <div class="flex-line">
        <?php CategoryGridRenderer::create()
            ->setImgName("equipement_courscollectif")
            ->setTitle("COURS COLLECTIF")
            ->renderItem($cats['coaching_sportif']);
        ?>
        <?php CategoryGridRenderer::create()
            ->setImgName("equipement_yoga")
            ->setTitle("YOGA")
            ->renderItem($cats['pilates_yoga']);
        ?>
        <?php CategoryGridRenderer::create()
            ->setImgName("equipement_cardio")
            ->setTitle("CARDIO<br>TRAINING")
            ->renderItem($cats['cardio_training']);
        ?>
    </div>
    <div class="flex-line">
        <?php CategoryGridRenderer::create()
            ->setImgName("equipement_pilates")
            ->setTitle("PILATES")
            ->renderItem($cats['pilates_yoga']);
        ?>
        <?php CategoryGridRenderer::create()
            ->setImgName("video2")
            ->setSpaceTaken(2)
            ->setVideoOverlay(true)
            ->setUri("https://www.youtube.com/watch?v=nwFmINpfVk4")
            ->renderItem($cats['pilates_yoga']);
        ?>
    </div>
    <div class="flex-line">
        <?php CategoryGridRenderer::create()
            ->setImgName("equipement_aqua")
            ->setTitle("SPORT<br>AQUATIQUE")
            ->renderItem($cats['pilates_yoga']);
        ?>
        <?php CategoryGridRenderer::create()
            ->setImgName("equipement_coachingsportif")
            ->setTitle("COACHING<br>SPORTIF")
            ->renderItem($cats['coaching_sportif']);
        ?>
        <?php CategoryGridRenderer::create()
            ->setImgName("equipement_amenagement")
            ->setTitle("AMÉNAGEMENT")
            ->renderItem($cats['amenagement']);
        ?>
    </div>
</div>
