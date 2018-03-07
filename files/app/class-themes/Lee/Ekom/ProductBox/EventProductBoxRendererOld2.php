<?php


namespace Theme\Lee\Ekom\ProductBox;


use Bat\StringTool;

class EventProductBoxRendererOld2 extends TrainingProductBoxRenderer
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

    protected function renderQuantityLine(array $model)
    {

        $qty = (true === $model['quantityInStock']) ? '1' : '0';

        ?>
        <div class="line f-start-end">
            <div class="quantity">
                <div class="title">Quantité</div>
                <input class="quantity-input" type="number" value="<?php echo $qty; ?>">
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
                                <li data-ajax="<?php echo htmlspecialchars($item['uriProductAjax']); ?>"
                                    data-key="<?php echo $key; ?>"
                                    data-date="<?php echo $item['date']; ?>"
                                    class="refresh-trigger <?php echo $sClass; ?>"><a class="refresh-trigger"
                                                                                      href="#"><?php echo $item['label']; ?></a>
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
                                            <?php echo $course['start_time'] . ' - ' . $course['end_time']; ?>
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
                                                                data-ecid="<?php echo $course['id']; ?>"
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

        <script>


            <?php if(true === $this->useDomContentLoaded): ?>
            document.addEventListener("DOMContentLoaded", function (event) {
                <?php endif; ?>
                $(document).ready(function () {


                    var api = ekomApi.inst();

                    var jBox = $('#<?php echo $boxCssId; ?>');
                    var jContainer = $('#<?php echo $id; ?>');
                    var jOptions = jContainer.find(".cell-items-container");
                    var jTableContainer = jContainer.find(".event-courses-planning-container");
                    var eventType = '<?php echo $eventType; ?>';
                    var eventId = <?php echo $eventId; ?>;
                    var uriCardAjax = '<?php echo $uriCardAjax; ?>';
                    var _options = <?php echo json_encode($options); ?>;
                    var _stockQuantity = <?php echo $model['quantity']; ?>;
                    var _productDetails = <?php echo json_encode($productDetails); ?>;

                    var _courseIds = [];
                    var _token = null;
                    var _isSelectChange = false;


                    function initTable() {
                        jTableContainer.find("select").each(function () {
                            $(this).off("change.event").on("change.event", function () {
                                var jForm = $(this).closest('form');
                                var s = jForm.serialize();
                                var uri = encodeURI(uriCardAjax) + '&' + s;
                                window.ekomRefreshProductBox(uri, jBox);
                            });
                        });
                    }


                    function renderSelectOptions(course) {
                        var s = '';
                        s += '<option value="0">Réserver...</option>';
                        for (var k = 1; k <= course['remaining_quantity']; k++) {
                            var sSel = (k == course['selected_qty']) ? 'selected="selected"' : '';
                            s += '<option ' + sSel + ' value="' + k + '">' + k + '</option>';
                        }
                        return s;
                    }


                    function updateOptionsByBoxModel(boxModel) {

                        uriCardAjax = boxModel.uriCardAjax;
                        _productDetails = boxModel.productDetails;


                        var eventDetails = boxModel.eventDetails;
                        var useCalendar = eventDetails.useCalendar;
                        var options = eventDetails.options;
                        _options = options;

                        var optionsTitle = eventDetails.optionsTitle;
                        eventId = eventDetails.id;
                        eventType = eventDetails.type;

                        if (true === useCalendar) {


                            jOptions.hide();

                            jTableContainer.show();
                            jTableContainer.empty();
                            var days = options.days;
                            var selectName = options.getName;

                            var s = '';

                            s += '<form method="get" action="">';
                            for (var i in days) {
                                var day = days[i];

                                s += '<table class="event-courses-planning">';
                                s += '<tr class="header">';
                                s += '<th colspan="3">';
                                s += day['dayLabel'];
                                s += ' ( ' + day['date'] + ' ) ';
                                s += '</th>';
                                s += '</tr>';

                                for (var j in day['courses']) {
                                    var course = day['courses'][j];
                                    s += '<tr>';
                                    s += '<td class="nowrap">';
                                    s += course['start_time'] + ' - ' + course['end_time'];
                                    s += '</td>';
                                    s += '<td class="fwidth">';
                                    s += '<table class="inner">';
                                    s += '<tr class="clickable">';

                                    s += '<td class="fwidth">';
                                    s += course['label'];
                                    s += '</td>';

                                    s += '<td class="right right-2">';
                                    s += '<select name="' + selectName + '[' + course['id'] + ']" data-ecid="' + course['id'] + '">';
                                    s += renderSelectOptions(course);
                                    s += '</select>';
                                    s += '</td>';

                                    s += '<td class="right">';
                                    s += '( Reste <span data-cvq="' + course['id'] + '">' + course['remaining_quantity'] + '</span> places sur ' + course['capacity'] + ')';
                                    s += '</td>';

                                    s += '</tr>';
                                    s += '</table>';
                                    s += '</td>';
                                    s += '</tr>';
                                }
                                s += '</table>';
                            }
                            s += '</form>';

                            jTableContainer.append(s);


                            var cartModel = window.cartModel; // from the cartSpreader
                            injectCartQuantitiesInCourses(cartModel);


                            var jAvail = jBox.find('.availability-events');
                            if(jAvail.hasClass('availability-0')){
                                jAvail.find('.availability-out-of-stock').html("Veuillez choisir au moins cours");
                            }

                        }
                        else {
//                                refreshCourseIdsForPassByOptions(options);
                            _stockQuantity = boxModel.quantity;
                            jOptions.show();
                            jTableContainer.hide();

                            jOptions.empty();
                            var s;


                            jOptions.append('<div class="cell-items"><div class=title>' + optionsTitle + '</div><ul></ul></div>');
                            var jUl = jOptions.find('ul');

                            for (var name in options) {
                                var item = options[name];


                                var sClass = '';
                                if (true === item["isSelected"]) {
                                    sClass = "active";
                                }

                                s =
                                    '<li data-ajax="' + item['uriProductAjax'] + '"' +
                                    ' data-key="' + name + '" ' +
                                    ' data-date="' + item['date'] + '" ' +
                                    'class="refresh-trigger ' + sClass + '"><a class="refresh-trigger"' +
                                    'href="#">' + item['label'] + '</a>' +
                                    '</li>';

                                jUl.append(s);
                            }
                        }


                        initTable();

                    }


                    //----------------------------------------
                    //
                    //----------------------------------------
                    var api = ekomApi.inst();
                    api.on('gui.onProductBoxUpdatedAfter', function (boxModel) {
                        _isSelectChange = true;
                        updateOptionsByBoxModel(boxModel);
                        _isSelectChange = false;
                    });

                    api.on('productBox.collectDetails', function (details) {
                        for (var k in _productDetails) {
                            details[k] = _productDetails[k];
                        }
                    });


                    /**
                     * This function is quite complex to understand, I'll do my best to explain it to you.
                     *
                     * It is created to handle different use cases:
                     *
                     * - if the user lands on the page and there is no "cours minor params" in the uri,
                     *      we do the routine.
                     * - if the user lands on the page and there are some "cours minor params" in the uri,
                     *      we consider that the user either comes from the order history, or from the cart update.
                     *      In both cases, the gui will naturally react to the params in the uri, we do nothing.
                     *
                     * - if the user switch to another attribute and comes back again, we do the routine.
                     *      This will give the illusion of gui persistency, which is usually what users expect
                     *
                     *
                     */
                    function injectCartQuantitiesInCourses(cartModel) {
                        if ('cours' === eventType) {

                            // if no param in the uri
                            if (0 === _productDetails.minor.dy.length && false === _isSelectChange) {


                                for (var i in cartModel.items) {
                                    var item = cartModel.items[i];
                                    if ('eventDetails' in item) {
                                        if ('dy' in item.productDetails.minor) {
                                            var dy = item.productDetails.minor.dy;
                                            for (var courseEventId in dy) {
                                                var cartQty = dy[courseEventId];
                                                var jSelect = jTableContainer.find('[data-ecid="' + courseEventId + '"]');
                                                jSelect.val(cartQty);
                                            }

                                            jBox.find('.price-value').html(item.salePrice);
                                            window.ekomUpdateProductBoxQuantity(item.stock_quantity, jBox);

                                        }
                                    }
                                }
                            }
                        }
                    }


                    api.on('cartModel.ready', function (cartModel) {
                        injectCartQuantitiesInCourses(cartModel);
                    });


                    api.on('productBox.ready', function () {
                        initTable();
                    });


                });
                <?php if(true === $this->useDomContentLoaded): ?>
            });
            <?php endif; ?>
        </script>
        <?php
    }
}