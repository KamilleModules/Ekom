<?php

use Kamille\Architecture\ApplicationParameters\ApplicationParameters;

$prefix = "/theme/" . ApplicationParameters::get("theme");

?>
<div class="two-banners window">
    <div class="banner banner-left">
        <a href="#">
            <img width="627" src="<?php echo $prefix; ?>/img/banners/half/debouches.jpg" alt="slide half left">
        </a>
    </div>
    <div class="banner banner-right">
        <a href="#">
            <img width="627" src="<?php echo $prefix; ?>/img/banners/half/offre-emploi.jpg" alt="slide half right"
            >
        </a>
    </div>
</div>
