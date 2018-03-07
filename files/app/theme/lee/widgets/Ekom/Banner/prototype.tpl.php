<?php

use Kamille\Architecture\ApplicationParameters\ApplicationParameters;

$prefix = "/theme/" . ApplicationParameters::get("theme");

?>
<div class="promo-banner">
    <a href="#" style="position: static">
        <img src="<?php echo $prefix; ?>/img/slides/slide-middle-600x260.jpg" alt="slide middle"
             srcset="<?php echo $prefix; ?>/img/slides/slide-middle-600x260.jpg 600w, <?php echo $prefix; ?>/img/slides/slide-middle-960.jpg 960w,
 <?php echo $prefix; ?>/img/slides/slide-middle-1440.jpg 1440w,  <?php echo $prefix; ?>/img/slides/slide-middle-1915.jpg 1915w"
        >
        <span class="title">NOUVEAUTÉS</span>
        <span class="subtitle">DÉCOUVREZ NOTRE NOUVELLE GAMME</span>
        <a href="#">VOIR LES ARTICLES</a>
    </a>
</div>