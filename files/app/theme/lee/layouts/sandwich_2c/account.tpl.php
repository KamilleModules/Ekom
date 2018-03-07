<?php

use Kamille\Utils\ThemeHelper\KamilleThemeHelper;
use Module\Ekom\Utils\E;

KamilleThemeHelper::css("layout-account.css");

?>

<?php $l->includes("common.php"); ?>
<?php $l->includes("top.php"); ?>


    <div class="layout layout-account window">

        <?php $l->position('breadcrumbs'); ?>

        <div class="title t-raleway-bold">
            <a href="<?php
            /**
             * @todo-ling: estimate whether or not the comment below should be fixed?
             */
            // NOte: template shouldn't use api features directly?
            echo E::link("Ekom_customerDashboard") . "?t=a";

            ?>">MON ESPACE CLIENT</a>
        </div>
        <div class="main">
            <div class="sidebar">
                <?php $l->position('sidebar'); ?>
            </div>
            <div class="main-content">
                <?php $l->position('maincontent'); ?>
            </div>
        </div>
    </div>


<?php $l->includes("bottom.php"); ?>