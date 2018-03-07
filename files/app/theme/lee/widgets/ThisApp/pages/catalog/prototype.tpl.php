<?php

use Kamille\Utils\ThemeHelper\KamilleThemeHelper;

KamilleThemeHelper::css('widgets/widget-catalogues.css');
?>
<div class="widget widget-catalogues window">

    <h2 class="widget-title">DÉCOUVREZ NOS CATALOGUES EN LIGNE À LIRE OU À TÉLÉCHARGER</h2>
    <ul class="catalogues-list">
        <?php for ($i = 1; $i <= 3; $i++): ?>
            <li class="item">
                <div class="image">
                    <a href="#">
                    <img src="/img/leaderfit/catalogue/catalogue-complet-2017-2018.jpg" alt="the alt">
                    </a>
                </div>
                <div class="title">Catalogue complet 2017-2018</div>
                <div class="action">
                    <a class="lee-red-button" href="#">TÉLÉCHARGER</a>
                    <span class="blank"></span>
                    <a class="lee-red-button"  href="#">LIRE</a>
                </div>
            </li>
        <?php endfor; ?>
    </ul>
</div>