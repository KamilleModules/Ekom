<?php


namespace Module\Ekom\Helper;

class BoxHelper
{

    public static function debugBoxes(array $boxes, array $properties)
    {
        ?>
        <table><?php
        foreach ($boxes as $box) {
            ?>
            <tr>
                <?php foreach ($properties as $property): ?>
                    <td><?php echo $property; ?>: <?php echo $box[$property]; ?></td>
                <?php endforeach; ?>
            </tr>
            <?php
        }
        ?></table><?php
    }
}