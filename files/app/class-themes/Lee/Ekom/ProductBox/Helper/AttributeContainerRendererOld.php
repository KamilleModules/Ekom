<?php


namespace Theme\Lee\Ekom\ProductBox\Helper;


class AttributeContainerRendererOld
{
    public static function renderAttributeContainer($title, array $items, $id = null)
    {
        $sId = (null === $id) ? '' : 'id="' . $id . '"';
        ?>

        <div class="cell-items-container" <?php echo $sId; ?>>
            <div class="cell-items">
                <div class="title"><?php echo $title; ?></div>
                <ul>
                    <?php foreach ($items as $item):

                        $sClass = '';
                        if (true === $item['isSelected']) {
                            $sClass .= "active";
                        }
                        ?>
                        <li data-ajax="<?php echo htmlspecialchars($item['uriAjax']); ?>"
                            data-key="<?php echo htmlspecialchars($item['value']); ?>"
                            class="refresh-trigger <?php echo $sClass; ?>">
                            <a class="refresh-trigger" href="#"><?php echo $item['label']; ?></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <?php
    }

    public static function renderAttributeContainerAsSelect($title, array $items, $id = null)
    {
        $sId = (null === $id) ? '' : 'id="' . $id . '"';

        ?>

        <div class="cell-items-container" <?php echo $sId; ?>>
            <div class="cell-items">
                <div class="title"><?php echo $title; ?></div>
                <select class="s-simple-select">
                    <?php foreach ($items as $item):
                        $sSelected = '';
                        if (true === $item['isSelected']) {
                            $sSelected .= 'selected="selected"';
                        }
                        ?>
                        <option <?php echo $sSelected; ?>
                                value="<?php echo htmlspecialchars($item['value']); ?>"
                                data-ajax="<?php echo htmlspecialchars($item['uriAjax']); ?>"
                                class="attribute-item"><?php echo $item['label']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <?php
    }
}