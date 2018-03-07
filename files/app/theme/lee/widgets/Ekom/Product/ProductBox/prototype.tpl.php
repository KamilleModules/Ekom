<?php

use Kamille\Architecture\ApplicationParameters\ApplicationParameters;

$prefix = "/theme/" . ApplicationParameters::get("theme");

?>
<div class="window pt20">
    <img src="<?php echo $prefix; ?>/img/prototype/product-box.jpg" class="mauto">
</div>

