<?php

use Module\ThisApp\Config\ThisAppViewHelper;
use Theme\Lee\Ekom\Helper\ListFilterHelper;
use Theme\LeeTheme;

LeeTheme::useLib("prettyCheckbox");

$cities = $v['cities'];

?>
<div class="widget widget-listfilter-events widget-listfilter" id="widget-listfilter-cities">
    <div class="listfilter-box">
        <?php ListFilterHelper::renderListFilterTitle("Lieu"); ?>
        <div class="listfilter-body">


            <?php foreach ($cities as $countryItem): ?>

                <section>
                    <h4><?php echo $countryItem['country_label']; ?></h4>
                    <?php foreach ($countryItem['cities'] as $city):
                        $sel = (true === $city["selected"]) ? 'checked="checked"' : "";
                        ?>
                        <div class="location-box cb-toggler">
                            <div class="pretty <?php echo ThisAppViewHelper::getPrettyCheckboxFlavour(); ?> cb-toggler">
                                <input type="checkbox" <?php echo $sel; ?> class=" cb-toggler"/>
                                <div class="state  cb-toggler">
                                    <label class=" cb-toggler">
                                        <a class="listfilter-link cb-toggler"
                                           href="<?php echo $city['uri']; ?>"><?php echo $city['label']; ?></a>
                                    </label>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </section>
            <?php endforeach; ?>

        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function (event) {
        $(document).ready(function () {
            $('#widget-listfilter-cities').on('click', function (e) {
                var jTarget = $(e.target);
                if (jTarget.hasClass("cb-toggler")) {
                    var uri = jTarget.closest(".location-box").find(".listfilter-link").attr("href");
                    window.location.href = uri;
                    return false;
                }
            });
        });
    });
</script>