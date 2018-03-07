<?php

use Kamille\Utils\ThemeHelper\KamilleThemeHelper;

KamilleThemeHelper::css("product-box.css");


?>
<div class="widget widget-product-card-error window2">
    <h2 class="error-title"><?php echo $v['errorTitle']; ?></h2>
    <p class="error-message"><?php echo $v['errorMessage']; ?></p>
</div>