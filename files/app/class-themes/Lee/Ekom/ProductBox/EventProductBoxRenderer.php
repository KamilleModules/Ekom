<?php


namespace Theme\Lee\Ekom\ProductBox;


use Bat\StringTool;

class EventProductBoxRenderer extends TrainingProductBoxRenderer
{

    public function __construct()
    {
        parent::__construct();
        $this->linkDescriptionLink = "#widget-product-events-agenda";
        $this->textDescriptionLink = "Planning";
        $this->styleDescriptionLink = 'style="margin-top: 5px;"';
        $this->positionRenderStock = 2;
        $this->cssBaseClass = 'events';
        $this->cssWidgetClass = "events";
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    protected function renderBlocBeforeAttributes(array $model)
    {

    }

    protected function renderRefDebugString(array $box)
    {
        ?>
        (#<?php echo $box['product_id']; ?>
        -<?php echo $box['card_id']; ?>)
        <?php
    }

    protected function renderQuantityLine(array $model)
    {

        $qty = (true === $model['quantityInStock']) ? '1' : '0';

        ?>
        <div class="line f-start-end">
            <div class="quantity">
                <div class="title">Quantité</div>
                <div class="pretty-input-number">
                    <input type="number" class="quantity-input bionic-target" data-id="quantity"
                           value="<?php echo $qty; ?>">
                </div>
            </div>
            <div class="add-to-bookmarks">
                <a class="bookmarks add-to-bookmarks" href="#">Ajouter à ma liste</a>
            </div>
        </div>
        <?php
    }


    protected function renderBelowAttributes(array $model)
    {
        $boxCssId = $this->getBoxCssId();

        $productDetails = $model['productDetails'];


        $details = $model['eventDetails'];
        $useCalendar = $details['useCalendar'];
        $options = $details['options'];
        $optionsTitle = $details['optionsTitle'];
        $id = StringTool::getUniqueCssId();

        $eventType = $details['type']; // cours | 1-jour | 2-jours
        $eventId = $details['id'];
        $uriCardAjax = $model['uriCardAjax'];


        ?>
        <div id="<?php echo $id; ?>">
            <?php
            if (false === $useCalendar):
                ?>
                <div class="cell-items-container">

                    <div class="cell-items">
                        <div class="title"><?php echo $optionsTitle; ?></div>
                        <ul>
                            <?php foreach ($options as $key => $item):

                                $sClass = '';
                                if (true === $item['isSelected']) {
                                    $sClass .= "active";
                                }

                                ?>
                                <li class="bionic-btn <?php echo $sClass; ?>"
                                    data-action="product.getInfo"
                                    data-param-product_id="<?php echo $item['product_id']; ?>"
                                    data-param-details-day="<?php echo $item['day']; ?>"
                                ><a href="#"><?php echo $item['label']; ?></a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                <div class="event-courses-planning-container" style="display: none"></div>

            <?php else: ?>

                <?php

                $days = $options['days'];
                $selectName = $options['getName'];


                ?>


                <div class="cell-items-container" style="display: none"></div>
                <div class="event-courses-planning-container">

                    <form method="get" action="">
                        <?php foreach ($days as $day): ?>
                            <table class="event-courses-planning">
                                <tr class="header">
                                    <th colspan="3">
                                        <?php echo $day['dayLabel']; ?>
                                        ( <?php echo $day['date']; ?> )
                                    </th>
                                </tr>

                                <?php foreach ($day['courses'] as $course): ?>
                                    <tr>
                                        <td class="nowrap">
                                            <?php
//                                            az($course);
                                            echo $course['start_time'] . ' - ' . $course['end_time']; ?>
                                        </td>
                                        <td class="fwidth">
                                            <table class="inner">
                                                <tr class="clickable">
                                                    <td class="fwidth">
                                                        <?php echo $course['label']; ?>
                                                        #<?php echo $course['id']; ?>
                                                    </td>
                                                    <td class="right right-2">
                                                        <select name="<?php echo $selectName; ?>[<?php echo $course['id']; ?>]"
                                                                class="bionic-select"
                                                                data-action="product.getInfo"
                                                                data-param-product_id="<?php echo $course['product_id']; ?>"
                                                                data-param-details-dy=":formValue:dy"


                                                        >
                                                            <option value="0">Réserver...</option>
                                                            <?php for ($i = 1; $i <= $course['remaining_quantity']; $i++):
                                                                $sSel = ($i === (int)$course['selected_qty']) ? 'selected="selected"' : '';
                                                                ?>
                                                                <option <?php echo $sSel; ?>
                                                                        value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                                            <?php endfor; ?>
                                                        </select>
                                                    </td>
                                                    <td class="right">
                                                        ( Reste <span
                                                                data-cvq="<?php echo $course['id']; ?>"><?php echo $course['remaining_quantity']; ?></span>
                                                        places
                                                        sur <?php echo $course['capacity']; ?> )
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>

                                    </tr>
                                <?php endforeach; ?>

                            </table>
                        <?php endforeach; ?>
                    </form>
                </div>

            <?php endif; ?>
        </div>
        <?php
    }
}