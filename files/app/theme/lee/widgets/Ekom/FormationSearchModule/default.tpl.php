<?php


use Bat\LocaleTool;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Kamille\Utils\ThemeHelper\KamilleThemeHelper;
use Module\Ekom\Utils\E;
use Module\ThisApp\Config\CityHelper;
use Module\ThisApp\Helper\ThisAppHelper;
use Theme\LeeTheme;

KamilleThemeHelper::css("widgets/widget-search-module.css");


$lang = ApplicationRegistry::get("ekom.lang_iso");


LeeTheme::useLib("simpleselect");
//LeeTheme::useLib("datepicker", $pickerLang);

$iso639_1 = LocaleTool::getLangIso639_1ByIso639_2($lang);

LeeTheme::useLib("jqueryUiDatePicker", $iso639_1);


$country = strtolower($v['country']);
$countries = $v['countries'];
$form = $v['form'];

$city2Coordinates = CityHelper::getCity2Coordinates();


?>
<div
        id="widget-training-search-module"
        class="widget widget-training-search-module widget-search-module window pt20">
    <div class="col-left">
        <div class="map main-<?php echo $country; ?>">
            <div class="title t-ralewaw">NOS FORMATIONS PRÈS DE CHEZ VOUS</div>
            <?php switch ($country):
                case 'fr': ?>
                    <a href="<?php echo $countries['US']['uri']; ?>" class="mini-1"><span>USA</span></a>
                    <a href="<?php echo $countries['BE']['uri']; ?>" class="mini-2"><span>Belgique</span></a>
                    <a href="<?php echo $countries['LU']['uri']; ?>" class="mini-3"><span>Luxembourg</span></a>
                    <?php break; ?>
                <?php case "us"; ?>
                    <a href="<?php echo $countries['FR']['uri']; ?>" class="mini-1"><span>France</span></a>
                    <a href="<?php echo $countries['BE']['uri']; ?>" class="mini-2"><span>Belgique</span></a>
                    <a href="<?php echo $countries['LU']['uri']; ?>" class="mini-3"><span>Luxembourg</span></a>
                    <?php break; ?>
                <?php case "be"; ?>
                    <a href="<?php echo $countries['US']['uri']; ?>" class="mini-1"><span>USA</span></a>
                    <a href="<?php echo $countries['LU']['uri']; ?>" class="mini-2"><span>Luxembourg</span></a>
                    <a href="<?php echo $countries['FR']['uri']; ?>" class="mini-3"><span>France</span></a>
                    <?php break; ?>
                <?php case "lu"; ?>
                    <a href="<?php echo $countries['US']['uri']; ?>" class="mini-1"><span>USA</span></a>
                    <a href="<?php echo $countries['BE']['uri']; ?>" class="mini-2"><span>Belgique</span></a>
                    <a href="<?php echo $countries['FR']['uri']; ?>" class="mini-3"><span>France</span></a>
                    <?php break; ?>
                <?php endswitch; ?>

            <div class="main">
                <?php
                $c = 0;
                foreach ($v['pins'] as $pin):

                    $pinDisplayName = $pin['name'];
                    if (
                        'arcueil' === $pin['name'] ||
                        'paris' === $pin['name']
                    ) {
                        $pinDisplayName = 'paris/arcueil';
                    }

                    list($left, $top) = $city2Coordinates[$pinDisplayName];
                    ?>
                    <div
                            data-pin-name="<?php echo $pin['name']; ?>"
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
                            <?php echo $pin['nbTrainings']; ?> sessions
                        </a>
                    </div>

                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div class="col-right">
        <form action="" method="get" class="search-training-module-form">
            <div class="header">
                <span>RECHERCHE RAPIDE</span>
                <i class="magnifying-glass"></i>
            </div>
            <table>
                <tr>
                    <td>PAYS</td>
                    <td>
                        <select name="country">
                            <option value="0">Tous les pays</option>
                            <?php foreach ($form['country'] as $value => $label):
                                ?>
                                <option value="<?php echo htmlspecialchars($value); ?>"><?php echo $label; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>VILLE</td>
                    <td>
                        <select name="city">
                            <option value="0">Toutes les villes</option>
                            <?php foreach ($form['city'] as $value => $label):
                                ?>
                                <option value="<?php echo htmlspecialchars($value); ?>"><?php echo $label; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>DPT DE FORMATION</td>
                    <td>
                        <select name="category">
                            <option value="0">Tous les départements</option>
                            <?php foreach ($form['category'] as $value => $label):
                                ?>
                                <option value="<?php echo htmlspecialchars($value); ?>"><?php echo $label; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>FORMATION</td>
                    <td>
                        <select name="card">
                            <option value="0">Toutes les formations</option>
                            <?php foreach ($form['card'] as $value => $label):
                                ?>
                                <option value="<?php echo htmlspecialchars($value); ?>"><?php echo $label; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>DATE DE DÉBUT</td>
                    <td>
                        <input class="datepicker" type="text" name="date_start" value="">
                    </td>
                </tr>
                <tr>
                    <td>DATE DE FIN</td>
                    <td>
                        <input class="datepicker" type="text" name="date_end" value="">
                    </td>
                </tr>
            </table>
            <div class="search-footer">
                <button class="lee-red-button submit-btn" type="submit">VALIDER</button>
                <br>
                <a href="<?php echo $v['uriAllTrainings']; ?>">> VOIR TOUTES LES SESSIONS</a>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function (event) {
        $(document).ready(function () {

            var jContext = $('#widget-training-search-module');
            var jMap = $('.map', jContext);
            var jForm = $('.search-training-module-form', jContext);
            var api = ekomApi.inst();


            var jCountry = jForm.find('select[name="country"]');
            var jCategory = jForm.find('select[name="category"]');
            var jCard = jForm.find('select[name="card"]');
            var jCity = jForm.find('select[name="city"]');
            //
            var jDateStart = jForm.find('input[name="date_start"]');
            var jDateEnd = jForm.find('input[name="date_end"]');


            var openPin = null;
            var formActionFormat = "<?php echo $v['formActionFormat']; ?>";
            var rootCategorySlug = "<?php echo $v['rootCategorySlug']; ?>";


            function feedSelect(jSelect, list, isCard) {
                var jOptZero = jSelect.find('option[value="0"]');
                jSelect.empty();
                jSelect.append(jOptZero);

                if (true === isCard) {
                    var arr = [];
                    for (var i in list) {
                        arr.push({
                            key: i,
                            value: list[i]
                        });
                    }
                    list = arr.sort(function (a, b) {
                        return a.value.localeCompare(b.value);
                    });
                    for (var value in list) {
                        var item = list[value];
                        jSelect.append('<option value="' + item.key + '">' + item.value + '</option>');
                    }
                }
                else {
                    for (var value in list) {
                        var label = list[value];
                        jSelect.append('<option value="' + value + '">' + label + '</option>');
                    }
                }
            }

            function updateSearchForm(options, name) {
                o = $.extend({
                    country: null,
                    city: null,
                    category: null,
                    card: null
                }, options);


                api.utils.request("EkomTrainingProducts:updateSearchForm", {
                    select: name,
                    country: o.country,
                    city: o.city,
                    category: o.category,
                    card: o.card
                }, function (model) {


                    feedSelect(jCountry, model.countryList);
                    feedSelect(jCity, model.cityList);
                    feedSelect(jCategory, model.categoryList);
                    feedSelect(jCard, model.cardList, true);

                    jCountry.find('option[value="' + model.country + '"]').prop('selected', true);
                    jCity.find('option[value="' + model.city + '"]').prop('selected', true);
                    jCategory.find('option[value="' + model.category + '"]').prop('selected', true);
                    jCard.find('option[value="' + model.card + '"]').prop('selected', true);


                    jCountry.simpleselect("refreshContents");
                    jCity.simpleselect("refreshContents");
                    jCategory.simpleselect("refreshContents");
                    jCard.simpleselect("refreshContents");


                });

            }


            jContext.find('select').each(function () {
                $(this).simpleselect();
                $(this).on('change', function () {
                    var opts = {
                        country: jCountry.val(),
                        city: jCity.val(),
                        category: jCategory.val(),
                        card: jCard.val()
                    };
                    updateSearchForm(opts, $(this).attr('name'));
                });
            });

            jForm.find('.submit-btn').on('click', function () {


                var country = jCountry.val();
                var categorySlug = jCategory.val();
                if ('0' === categorySlug) {
                    categorySlug = rootCategorySlug;
                }
                var card = jCard.val();
                var city = jCity.val();
                var dateStart = jDateStart.val();
                var dateEnd = jDateEnd.val();


                jCategory.removeAttr('name');

                if ('0' === country) {
                    jCountry.removeAttr('name');
                }
                if ('0' === card) {
                    jCard.removeAttr('name');
                }
                if ('0' === city) {
                    jCity.removeAttr('name');
                }
                if ('' === dateStart) {
                    jDateStart.removeAttr('name');
                }
                if ('' === dateEnd) {
                    jDateEnd.removeAttr('name');
                }

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

                jMap.on('click', function (e) {
                    var jTarget = $(e.target);
                    if (jTarget.hasClass("pin")) {
                        var city = jTarget.attr('data-pin-name');

                        var opts = {
                            country: jCountry.val(),
                            city: city,
                            category: jCategory.val(),
                            card: jCard.val()
                        };
                        updateSearchForm(opts, 'city');
                    }
                });
            }

            initPins();


            jForm.find('.datepicker').datepicker({
                dateFormat: "yy-mm-dd"
            });

        });
    });
</script>