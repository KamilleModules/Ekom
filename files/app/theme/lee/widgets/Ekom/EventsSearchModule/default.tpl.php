<?php


use Kamille\Utils\ThemeHelper\KamilleThemeHelper;
use Module\ThisApp\Config\CityHelper;
use Module\ThisApp\Helper\ThisAppHelper;
use OnTheFlyForm\Helper\OnTheFlyFormHelper;
use Theme\LeeTheme;

KamilleThemeHelper::css("widgets/widget-search-module.css");
LeeTheme::useLib("simpleselect");


$countries = $v['countries'];
$country = strtolower($v['country']);
$form = $v['form'];

$city2Coordinates = CityHelper::getCity2Coordinates();

?>
<div
        id="widget-events-search-module"
        class="widget widget-events-search-module widget-search-module window2 pt20">
    <div class="col-left">
        <div class="map main-<?php echo $country; ?>">
            <?php switch ($country):
                case 'fr': ?>
                    <a href="<?php echo $countries['US']['uri']; ?>" class="mini-1"></a>
                    <a href="<?php echo $countries['BE']['uri']; ?>" class="mini-2"></a>
                    <a href="<?php echo $countries['LU']['uri']; ?>" class="mini-3"></a>
                    <?php break; ?>
                <?php case "us"; ?>
                    <a href="<?php echo $countries['FR']['uri']; ?>" class="mini-1"></a>
                    <a href="<?php echo $countries['BE']['uri']; ?>" class="mini-2"></a>
                    <a href="<?php echo $countries['LU']['uri']; ?>" class="mini-3"></a>
                    <?php break; ?>
                <?php case "be"; ?>
                    <a href="<?php echo $countries['US']['uri']; ?>" class="mini-1"></a>
                    <a href="<?php echo $countries['LU']['uri']; ?>" class="mini-2"></a>
                    <a href="<?php echo $countries['FR']['uri']; ?>" class="mini-3"></a>
                    <?php break; ?>
                <?php case "lu"; ?>
                    <a href="<?php echo $countries['US']['uri']; ?>" class="mini-1"></a>
                    <a href="<?php echo $countries['BE']['uri']; ?>" class="mini-2"></a>
                    <a href="<?php echo $countries['FR']['uri']; ?>" class="mini-3"></a>
                    <?php break; ?>
                <?php endswitch; ?>

            <div class="main">
                <?php
                $c = 0;
                foreach ($v['pins'] as $pin):
                    list($left, $top) = $city2Coordinates[$pin['name']];
                    ?>
                    <div
                            data-pin="<?php echo $c; ?>"
                            style="left: <?php echo $left; ?>px; top: <?php echo $top; ?>px"
                            class="pin pin-<?php echo htmlspecialchars($pin['name']); ?>"
                    >
                    </div>


                    <div
                            data-pin-id="<?php echo $c++; ?>"
                            class="pin-info"
                            style="left: <?php echo $left + 35; ?>px; top: <?php echo $top; ?>px"

                    >
                        <a href="#">
                            <?php echo strtoupper($pin['name']); ?><br>
                            <?php echo $pin['nbEvents']; ?> événements
                        </a>
                    </div>

                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div class="col-right">
        <form action="" method="get" class="search-event-module-form">
            <div class="header">
                <span>RECHERCHE RAPIDE</span>
                <i class="magnifying-glass"></i>
            </div>
            <table>
                <tr>
                    <td>THÉMATIQUE</td>
                    <td>
                        <select name="category">
                            <?php foreach ($form['category'] as $item):
                                $sSel = (true === $item['selected']) ? ' selected="selected"' : '';
                                ?>
                                <option
                                    <?php echo $sSel; ?>
                                        value="<?php echo $item['value']; ?>"><?php echo $item['label']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>VILLE</td>
                    <td>
                        <select name="location">
                            <?php foreach ($form['city'] as $item):
                                $sSel = (true === $item['selected']) ? ' selected="selected"' : '';
                                ?>
                                <option
                                    <?php echo $sSel; ?>
                                        value="<?php echo $item['value']; ?>"><?php echo $item['label']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>DATES</td>
                    <td>
                        <select name="date">
                            <?php foreach ($form['date'] as $item):
                                $sSel = (true === $item['selected']) ? ' selected="selected"' : '';
                                ?>
                                <option
                                    <?php echo $sSel; ?>
                                        value="<?php echo $item['value']; ?>"><?php echo $item['label']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
            </table>
            <div class="search-footer">
                <button class="lee-red-button submit-btn" type="submit">VALIDER</button>
                <br>
                <a href="<?php echo $v['uriAllEvents']; ?>">> VOIR TOUS LES ÉVÉNEMENTS</a>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function (event) {
        $(document).ready(function () {

            var jContext = $('#widget-events-search-module');
            var jMap = $('.map', jContext);
            var jForm = $('.search-event-module-form', jContext);
            var jCity = jForm.find('select[name="location"]');
            var jDate = jForm.find('select[name="date"]');
            var openPin = null;
            var _country = "<?php echo $v['country']; ?>";
            var formActionFormat = "<?php echo $v['formActionFormat']; ?>";
            var api = ekomApi.inst();


            jContext.find('select').each(function () {
                $(this).simpleselect();
            });

            jForm.find('.submit-btn').on('click', function(){

                var jCat = jForm.find('select[name="category"]');
                var categorySlug = jCat.val();

                jCat.removeAttr('name');

                var action = formActionFormat.replace('%s', categorySlug);
                jForm.attr('action', action);
                jForm.submit();
                return false;
            });


            function initPins() {
                jMap.find('.pin').hover(function () {
                    openPin = $(this).attr("data-pin");
                    jMap.find('[data-pin-id="' + openPin + '"]').addClass("visible");
                }, function () {
                    jMap.find('[data-pin-id="' + openPin + '"]').removeClass("visible");
                });
            }


            function refreshSelector(items, jSelector) {
                var s = "";
                jSelector.empty();
                for (var i in items) {
                    var item = items[i];

                    var sSel = '';
                    if (true === item.selected) {
                        sSel = ' selected="selected"';
                    }
                    s += '<option ' + sSel + ' value="' + item.value + '">' + item.label + '</option>';
                }

                jSelector.append(s);
                jSelector.simpleselect("refreshContents");
            }

            function updateGuiByCategory(category) {
                api.utils.request('gscp', 'getCityAndDatesByCountryCategory', {
                    country: _country,
                    category: category
                }, function (model) {
                    var cities = model['city'];
                    var dates = model['date'];

                    refreshSelector(cities, jCity);
                    refreshSelector(dates, jDate);

                }, null, 'EkomEvents');
            }

            function updateGuiByCategoryCity(category, city) {
                api.utils.request('gscp', "getCityAndDatesByCountryCategoryCity", {
                    country: _country,
                    category: category,
                    city: city
                }, function (model) {
                    var dates = model['date'];
                    refreshSelector(dates, jDate);
                }, null, 'EkomEvents');

            }


            jForm.find('select[name="category"]').on('change', function () {
                var value = $(this).val();
                updateGuiByCategory(value);
            });

            jForm.find('select[name="location"]').on('change', function () {
                var category = jForm.find('select[name="category"]').val();
                var value = $(this).val();
                updateGuiByCategoryCity(category, value);
            });


            initPins();

        });
    });
</script>