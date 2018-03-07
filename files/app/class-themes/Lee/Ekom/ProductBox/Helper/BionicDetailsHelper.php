<?php


namespace Theme\Lee\Ekom\ProductBox\Helper;


class BionicDetailsHelper
{

    public static function renderBionicDetailsMap(array $details)
    {
        ?>
        <?php foreach ($details as $k => $v): ?>
        <?php if (!is_array($v)): ?>
            data-param-details-<?php echo $k; ?>="<?php echo $v; ?>"
        <?php else: ?>
            data-param-details-<?php echo $k; ?>=":json:<?php echo htmlspecialchars(json_encode($v, \JSON_FORCE_OBJECT)); ?>"
        <?php endif; ?>

    <?php endforeach; ?>
        <?php
    }

}