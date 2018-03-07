<?php

use Kamille\Utils\ThemeHelper\KamilleThemeHelper;
use OnTheFlyForm\Helper\OnTheFlyFormHelper;
use Theme\LeeTheme;


LeeTheme::useLib("onTheFlyForm");
KamilleThemeHelper::css("tool-form.css");
KamilleThemeHelper::css("table-form.css");
KamilleThemeHelper::css("customer-all.css");


?>
<div class="tool-form">
    <h1 class="bar-red">MES MOYENS DE PAIEMENT</h1>

    <?php $l->position('payment-methods'); ?>
</div>
