<?php

use Kamille\Architecture\ApplicationParameters\ApplicationParameters;
use Module\Ekom\Utils\E;

$prefix = "/theme/" . ApplicationParameters::get("theme");

?>

<div class="two-banners window">
    <div class="banner banner-left">
        <a href="<?php echo E::link("Ekom_productCard", ['productName' => "kettle-bell"]); ?>">
            <img width="627" src="<?php echo $prefix; ?>/img/banners/half/offre-du-moment.jpg" alt="slide half left">
        </a>
    </div>
    <div class="banner banner-right">
        <a href="#">
            <img width="627" src="<?php echo $prefix; ?>/img/banners/half/video-du-mois.jpg" alt="slide half right"
            >
        </a>
    </div>
</div>
