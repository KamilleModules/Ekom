<?php

use Kamille\Utils\ThemeHelper\KamilleThemeHelper;

KamilleThemeHelper::css("widgets/widget-related-training.css");


?>
<div class="widget widget-related-training window2">
    <div class="top-title">
        <span class="main">ILS SE SONT AUSSI INSCRITS</span>
    </div>

    <div class="list-training">
        <?php for ($i = 1; $i <= 3; $i++): ?>
            <div class="item">
                <div class="left-block">
                    <div class="line">
                        <div class="title">PETIT MATÉRIEL</div>
                        <div class="duration">2 JOURS - 14H</div>
                        <div class="price">460€</div>
                    </div>
                    <div class="line2">
                        Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ab cupiditate doloremque id labore
                        natus
                        placeat quo rem soluta ut vero. Cum eos incidunt necessitatibus nesciunt quod repellendus
                        tempora
                        voluptates voluptatum.
                    </div>
                    <div class="line3">
                        <a href="#" class="lee-red-button">PLUS D'INFORMATIONS</a>
                    </div>
                </div>
                <div class="right-block">
                    <img src="/img/products/balance-board.jpg" alt="image">
                </div>
            </div>
        <?php endfor; ?>
    </div>


</div>