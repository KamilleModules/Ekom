<?php

use Kamille\Architecture\ApplicationParameters\ApplicationParameters;
use Module\Ekom\Utils\E;

$prefix = "/theme/" . ApplicationParameters::get("theme");

?>
<div class="two-banners window">
    <div class="banner banner-left">
        <a href="<?php echo E::link("Ekom_productCard", ['slug' => 'pilates']); ?>">
            <img alt="slide half left"
                 sizes="(min-width: 300px) 70vw, 100vw"
                 srcset="<?php echo $prefix; ?>/img/slides/slide-half-left-310.jpg 310w,<?php echo $prefix; ?>/img/slides/slide-half-left-440.jpg 440w"
            >
        </a>
    </div>
    <div class="banner banner-right">
        <a href="<?php echo E::link("Ekom_productCard", ['slug' => 'kettle-bell']); ?>">
            <img src="/img/slides/slide-half-right.jpg" alt="slide half right"
                 sizes="(min-width: 300px) 70vw, 100vw"
                 srcset="<?php echo $prefix; ?>/img/slides/slide-half-right-310.jpg 310w, <?php echo $prefix; ?>/img/slides/slide-half-right-440.jpg 440w"
            >
        </a>
    </div>
</div>
