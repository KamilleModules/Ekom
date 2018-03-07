<?php


namespace Theme\Lee\Ekom\ProductBox;


use Bat\StringTool;
use Kamille\Services\XLog;

class EventProductBoxRendererOld extends TrainingProductBoxRenderer
{

    public function __construct()
    {
        parent::__construct();
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

        $qty = (true === $model['is_in_stock']) ? '1' : '0';

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
        $productIdentity = $model['productIdentity'];
        $details = $model['eventDetails'];


        $stockQuantities = array_key_exists('ekomEventsStockQuantities', $details) ? $details['ekomEventsStockQuantities'] : [];
        $cartQuantities = array_key_exists('ekomEventsCartQuantities', $details) ? $details['ekomEventsCartQuantities'] : [];
        $token = array_key_exists('token', $details) ? $details['token'] : null;
        $token = (string)$token;


        $useCalendar = $details['useCalendar'];
        $options = $details['options'];
        $optionsTitle = $details['optionsTitle'];
        $id = StringTool::getUniqueCssId();

        $eventType = $details['type']; // cours | 1-jour | 2-jours
        $eventId = $details['id'];
        $uriCardAjax = $model['uriCardAjax'];


        ?>
        <button id="apec">apec</button>
        <div id="apec-container"></div>
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
                                <li data-ajax="<?php echo htmlspecialchars($item['uri']); ?>"
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
                                                            <?php for ($i = 1; $i <= $course['virtual_quantity']; $i++):
                                                                $sSel = ($i === (int)$course['selected_qty']) ? 'selected="selected"' : '';
                                                                ?>
                                                                <option <?php echo $sSel; ?>
                                                                        value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                                            <?php endfor; ?>
                                                        </select>
                                                    </td>
                                                    <td class="right">
                                                        ( Reste <span
                                                                data-cvq="<?php echo $course['id']; ?>"><?php echo $course['virtual_quantity']; ?></span>
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
            document.addEventListener("DOMContentLoaded", function (event) {
                $(document).ready(function () {


                    var jApecContainer = $('#apec-container');
                    $('#apec').on('click', function () {
                        console.log('apec');
                        $.get('/apec.php', function (s) {
                            jApecContainer.append(s);
                        });
                        return false;
                    });


                    var api = ekomApi.inst();
                    api.on('productBox.ready', function () {


                        var jContainer = $('#<?php echo $id; ?>');
                        var jOptions = jContainer.find(".cell-items-container");
                        var jTableContainer = jContainer.find(".event-courses-planning-container");
                        var eventType = '<?php echo $eventType; ?>';
                        var eventId = <?php echo $eventId; ?>;
                        var uriCardAjax = '<?php echo $uriCardAjax; ?>';

                        var _stockQuantities = <?php echo json_encode($stockQuantities); ?>;
                        var _cartQuantities = <?php echo json_encode($cartQuantities); ?>;
                        var _options = <?php echo json_encode($options); ?>;
                        var _stockQuantity = <?php echo $model['quantity']; ?>;
                        var _courseIds = [];
                        var _token = null;

                        function initTable() {
                            jTableContainer.find("select").each(function () {
                                $(this).off("change.event").on("change.event", function () {
                                    var jForm = $(this).closest('form');
                                    var s = jForm.serialize();
                                    var uri = encodeURI(uriCardAjax) + '&' + s;
                                    window.ekomRefreshProductBox(uri);
                                });
                            });

                            if ('cours' === eventType) {
                                var isSelected = checkOneSelected();
                                if (false === isSelected) {
                                    var jBox = jTableContainer.closest(".product-box");
                                    jBox.find(".add-to-cart-btn, .add-to-estimate").addClass('disabled');
                                }
                            }
                            else {
                                refreshCourseIdsForPassByOptions(_options);
                                updateVirtualQuantities();
                            }
                        }


                        function refreshCourseIdsForPassByOptions(options) {
                            for (var i in options) {
                                var option = options[i];
                                if (true === option.isSelected) {
                                    _courseIds = option.courseIds;
                                    break;
                                }
                            }
                        }


                        function renderSelectOptions(course) {
                            var s = '';
                            s += '<option value="0">Réserver...</option>';
                            for (var k = 1; k <= course['virtual_quantity']; k++) {
                                var sSel = (k == course['selected_qty']) ? 'selected="selected"' : '';
                                s += '<option ' + sSel + ' value="' + k + '">' + k + '</option>';
                            }
                            return s;
                        }


                        function updateOptionsByBoxModel(boxModel) {
                            uriCardAjax = boxModel.uriCardAjax;
                            var eventDetails = boxModel.eventDetails;
                            var useCalendar = eventDetails.useCalendar;
                            var options = eventDetails.options;
                            _options = options;

                            var optionsTitle = eventDetails.optionsTitle;
                            eventId = eventDetails.id;
                            eventType = eventDetails.type;


                            if (true === useCalendar) {

                                _stockQuantities = eventDetails.ekomEventsStockQuantities;
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
                                        s += '( Reste <span data-cvq="' + course['id'] + '">' + course['virtual_quantity'] + '</span> places sur ' + course['capacity'] + ')';
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


                            }
                            else {
                                refreshCourseIdsForPassByOptions(options);
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
                                        '<li data-ajax="' + item['uri'] + '"' +
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


                        function extractNumber(input) {
                            var ret = input.match(/[0-9]+/g);
                            if (null !== ret) {
                                ret = ret[0];
                            }
                            return ret;
                        }


                        function checkOneSelected() {
                            var jForm = jTableContainer.find("form");
                            var items = jForm.serializeArray();
                            for (var i in items) {
                                var item = items[i];
                                if ("0" !== item['value']) {
                                    return true;
                                }
                            }
                            return false;
                        }

                        /**
                         * This function does two things:
                         * - update all virtual quantities in the table
                         * - update the globalVirtualQuantity box at the bottom of the product page
                         */
                        function updateVirtualQuantities() {
                            if ('cours' === eventType) {
                                var jForm = jTableContainer.find("form");

                                //----------------------------------------
                                // UPDATING ALL VIRTUAL QUANTITIES
                                //----------------------------------------
                                var allVirtualQties = [];
                                var stockQty, cartQty;
                                for (var id in _stockQuantities) {
                                    stockQty = _stockQuantities[id];
                                    var virtualQty = stockQty;
                                    if (id in _cartQuantities) {
                                        cartQty = _cartQuantities[id];
                                        virtualQty -= cartQty;
                                    }


                                    //----------------------------------------
                                    // recreating selectors
                                    //----------------------------------------
                                    jForm.find('[data-cvq="' + id + '"]').html(virtualQty);
                                    var jSelect = jForm.find('[data-ecid="' + id + '"]'); // updating the virtual quantity number
                                    var value = jSelect.val();

                                    jSelect.empty();
                                    var course = {
                                        selected_qty: value,
                                        virtual_quantity: virtualQty
                                    };
                                    var s = renderSelectOptions(course);
                                    jSelect.append(s);
                                    if ('0' !== value) {
                                        var v = Math.floor(virtualQty / value);
                                        allVirtualQties.push(v);
                                    }
                                }


                                //----------------------------------------
                                // UPDATING GLOBAL VIRTUAL QUANTITY
                                //----------------------------------------

                                if (allVirtualQties.length > 0) {
                                    var minVirtualQty = Math.min.apply(Math, allVirtualQties);
                                    if (minVirtualQty < 0) {
                                        minVirtualQty = 0;
                                    }
                                    window.ekomProductBoxUpdateVirtualQuantity(minVirtualQty);
                                }
                            }
                            else {

                                // find selected date
                                for (var i in _options) {
                                    var option = _options[i];
                                    if (true === option.isSelected) {

                                        var dateString = option.date;
                                        var dates = dateString.split('/');
                                        var qties = [];
                                        var virtuals = [];

                                        for (var j in dates) {
                                            var date = dates[j];

                                            qties = [];
                                            var courseIds = option.courseIds[date];
                                            for (var courseId in courseIds) {
                                                var virtualQty = courseIds[courseId]; // remainingQty
                                                if (courseId in _cartQuantities) {
                                                    virtualQty -= _cartQuantities[courseId];
                                                }
                                                qties.push(virtualQty);
                                            }
                                            if (qties.length > 0) {
                                                var minVirtual = Math.min.apply(Math, qties);
                                                if (minVirtual < 0) {
                                                    minVirtual = 0;
                                                }
                                                virtuals.push(minVirtual);
                                            }
                                        }

                                        if (virtuals.length > 0) {
                                            var minVirtualQty = Math.min.apply(Math, virtuals);
                                            window.ekomProductBoxUpdateVirtualQuantity(minVirtualQty);
                                        }

                                        break;
                                    }
                                }

                            }
                        }

                        //----------------------------------------
                        //
                        //----------------------------------------
                        var api = ekomApi.inst();
                        api.on('gui.onProductBoxUpdatedAfter', function (boxModel) {
                            updateOptionsByBoxModel(boxModel);
                        });

                        api.on('gui.onProductBoxUpdatedBefore', function (boxModel) {
                            if ('cours' !== eventType) {
                                window.ekomProductBoxUpdateVirtualQuantity(null);
                            }
                        });


                        api.on('cart.updated', function (cartModel) {
                            _cartQuantities = cartModel.ekomEventsCartQuantities;
                            updateVirtualQuantities();
                        });


                        api.on('productBox.collectDetailsForAddItemInCart', function (details, detailsParams, options) {

                            details.events = {
                                'id': eventId
                            };

                            switch (eventType) {
                                case '1-jour':
                                case '2-jours':
                                    var active = jContainer.find('.active').attr("data-key");
                                    var date = jContainer.find('.active').attr("data-date");
                                    detailsParams['day'] = active;
                                    details.events.date = date;
                                    break;
                                case 'cours':
                                    var jForm = jContainer.find('form');
                                    var id2qty = {};
                                    var data = jForm.serializeArray();
                                    for (var i in data) {
                                        var name = data[i]['name'];
                                        var qty = data[i]['value'];
                                        if ('0' !== qty) {
                                            var id = extractNumber(name);
                                            if (null !== id) {
                                                id2qty[id] = qty;
                                            }
                                        }
                                    }


                                    var token = "<?php echo $token; ?>";
                                    if (null === _token) {

                                        details.events['dy'] = id2qty;

                                        if ('' === token) {
                                            var d = new Date();
                                            token = d.getTime(); // generate random to make this a unique product
                                            _token = token;
                                        }
                                        else {
                                            options.type = 'replace';
                                            options.productIdentity = '<?php echo $productIdentity; ?>';
                                        }
                                    }
                                    else {
                                        /**
                                         * With this system, if the user creates a new product, she can continue
                                         * adding on this product w/o the token in the uri
                                         */
                                        token = _token;
                                        options.type = 'replace';
                                        options.productIdentity = '<?php echo $productIdentity; ?>';
                                    }


                                    detailsParams['token'] = token;
                                    break;
                            }
                        });


                        initTable();
                    });
                });
            });
        </script>
        <?php
    }
}