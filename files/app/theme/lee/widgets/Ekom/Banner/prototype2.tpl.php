<?php

use Kamille\Architecture\ApplicationParameters\ApplicationParameters;

$prefix = "/theme/" . ApplicationParameters::get("theme");

?>

<div class="promo-banner">
    <a href="#" style="position: static">
        <img src="<?php echo $prefix; ?>/img/slides/slide-bottom.jpg" alt="slide bottom">
        <span class="title">LF' COMMUNICATION</span>
        <span class="subtitle">BESOIN D'UN PACK DE COMMUNICATION</span>
        <a href="#">VOIR LES ARTICLES</a>
    </a>
</div>