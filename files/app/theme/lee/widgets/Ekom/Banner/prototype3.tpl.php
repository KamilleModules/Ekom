<?php

use Kamille\Architecture\ApplicationParameters\ApplicationParameters;

$prefix = "/theme/" . ApplicationParameters::get("theme");

?>
<div class="promo-banner window">
    <a href="#" style="position: static">
        <img src="<?php echo $prefix; ?>/img/banners/full/financez-votre-projet.jpg" alt="slide bottom">
    </a>
</div>