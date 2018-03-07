<?php

use Kamille\Utils\ThemeHelper\KamilleThemeHelper;

KamilleThemeHelper::css("customer/customer-account-menu.css");


$sections = $v['menu'];


?>
<div class="widget widget-customer-account-menu">
    <?php foreach ($sections as $section): ?>

        <div class="section">
            <div class="title"><?php echo $section['label']; ?></div>
            <div class="menu">
                <?php
                $items = $section['items'];
                if ($items): ?>
                    <ul>
                        <?php foreach ($items as $item): ?>
                            <li><a <?php echo (true === $item['active']) ? 'class="active"' : ''; ?>
                                        href="<?php echo $item['link']; ?>"><?php echo $item['label']; ?></a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>



