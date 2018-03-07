<?php


namespace Theme\Lee\Ekom\ProductBox\Helper;



class AttributeContainerRenderer
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
                        <li
                            class="bionic-btn <?php echo $sClass; ?>"
                            data-action="product.getInfo"
                            data-param-product_id="<?php echo $item['product_id']; ?>"
                            <?php self::renderBionicDetailsMap($item); ?>
                        >
                            <a href="#"><?php echo $item['label']; ?></a>
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
                <select class="s-simple-select bionic-select"
                        data-action="product.getInfo"
                        data-merge-option="1"
                >
                    <?php foreach ($items as $item):
                        $sSelected = '';
                        if (true === $item['isSelected']) {
                            $sSelected .= 'selected="selected"';
                        }
                        ?>
                        <option <?php echo $sSelected; ?>
                                value="<?php echo htmlspecialchars($item['value']); ?>"
                                data-ajax="<?php echo htmlspecialchars($item['uriAjax']); ?>"
                                class="attribute-item"
                                data-param-product_id="<?php echo $item['product_id']; ?>"
                            <?php self::renderBionicDetailsMap($item); ?>

                        ><?php echo $item['label']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <?php
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    private static function renderBionicDetailsMap(array $item){
        if(array_key_exists('details', $item)){
            BionicDetailsHelper::renderBionicDetailsMap($item['details']);
        }
    }


}