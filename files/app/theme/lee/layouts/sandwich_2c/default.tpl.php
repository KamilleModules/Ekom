<?php


use Kamille\Mvc\HtmlPageHelper\HtmlPageHelper;

HtmlPageHelper::addBodyClass("sandwich-2c");


?>
<?php $l->includes('common.php'); ?>
<?php $l->includes('top.php'); ?>


<div class="sandwich2-breadcrumbs">
    <?php $l->position('breadcrumbs'); ?>
</div>
<div class="sandwich-columns-container window">
    <div class="sidebar">
        <?php $l->position('sidebar'); ?>
    </div>

    <div class="maincontent">
        <?php $l->position('maincontent'); ?>
    </div>
</div>
<?php $l->includes('bottom.php'); ?>



