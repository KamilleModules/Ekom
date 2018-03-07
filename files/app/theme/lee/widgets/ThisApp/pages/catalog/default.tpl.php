<?php

use Kamille\Utils\ThemeHelper\KamilleThemeHelper;

KamilleThemeHelper::css('widgets/widget-catalogues.css');

$catalogs = $v['catalogs'];

?>
<div class="widget widget-catalogues window">

    <h2 class="widget-title">DÉCOUVREZ NOS CATALOGUES EN LIGNE À LIRE OU À TÉLÉCHARGER</h2>
    <ul class="catalogues-list">
        <?php foreach ($catalogs as $c): ?>
            <li class="item">
                <div class="image">
                    <a href="<?php echo $c['uriDownload']; ?>">
                        <img src="<?php echo $c['uriImage']; ?>" alt="<?php echo htmlspecialchars($c['altImage']); ?>">
                    </a>
                </div>
                <div class="title"><?php echo $c['title']; ?></div>
                <div class="action">
                    <a class="lee-red-button" href="<?php echo $c['uriDownload']; ?>">TÉLÉCHARGER</a>
                    <span class="blank"></span>
                    <a class="lee-red-button" href="<?php echo $c['uriRead']; ?>">LIRE</a>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</div>