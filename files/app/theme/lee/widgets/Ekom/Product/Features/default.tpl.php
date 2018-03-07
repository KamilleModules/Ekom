<?php

use Kamille\Architecture\ApplicationParameters\ApplicationParameters;
use Kamille\Utils\ThemeHelper\KamilleThemeHelper;
use Module\ThisApp\Helper\ThisAppHelper;


$prefix = "/theme/" . ApplicationParameters::get("theme");
KamilleThemeHelper::css("product-features.css");


$title = "FICHE TECHNIQUE";
if (true === ThisAppHelper::isTraining($v['seller'])) {
    $title = "+ D'INFOS";
}

?>
<div class="window pt20 widget widget-product-features" id="widget-product-features">

    <div class="line top-title">
        <span class="main"><?php echo $title; ?></span>
    </div>
    <div style="clear: both"></div>

    <div class="line features-line">
        <div class="features-description">
            <p>
                <?php if ('' !== $v['technical_description']): ?>
                    <?php echo $v['technical_description']; ?>
                <?php else: ?>
                    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Aspernatur autem distinctio excepturi odit
                    officiis quibusdam rerum suscipit? Adipisci assumenda atque debitis doloribus fuga inventore nisi nulla
                    repudiandae sed vel! Pariatur!
                <?php endif; ?>
            </p>
        </div>
        <?php if (count($v['features']) > 0): ?>
            <div class="features-details">
                <ul>
                    <?php foreach ($v['features'] as $item): ?>
                        <li>
                            <span class="feature-name"><?php echo $item['name']; ?>:</span> <span
                                    class="feature-value"><?php echo $item['value']; ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>

</div>

