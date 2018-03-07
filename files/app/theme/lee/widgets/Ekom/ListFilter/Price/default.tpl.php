<?php


use Theme\Lee\Ekom\Helper\ListFilterHelper;
use Theme\LeeTheme;

LeeTheme::useLib("jRange");
az($v);
?>
<?php if (array_key_exists('title', $v)): ?>

    <div class="widget widget-listfilter-attributes widget-listfilter-price widget-listfilter">

        <?php
        $a = $v;
        $jsMoneyFormat = str_replace([
            "s",
            "v",
        ], [
            $a['moneyFormatArgs']['moneySymbol'],
            '%s',
        ], $a['moneyFormatArgs']['moneyFormat']);


        ?>

        <div class="attribute-box listfilter-box">

            <?php ListFilterHelper::renderListFilterTitle($a['title']); ?>
            <div class="slider-box listfilter-body price-box">
                <div class="range-box">
                    <input data-format="<?php echo $jsMoneyFormat; ?>"
                           data-min="<?php echo $a['minValue']; ?>"
                           data-max="<?php echo $a['maxValue']; ?>"
                           data-current-min="<?php echo $a['currentMin']; ?>"
                           data-current-max="<?php echo $a['currentMax']; ?>"

                           type="text" class="range-slider"
                           value="<?php echo $a['currentMin'] . ',' . $a['currentMax']; ?>"/>
                </div>
                <div class="ok-box hidden">
                    <form method="get" action="">
                        <button type="submit">Ok</button>
                        <input class="name-input" type="hidden" name="<?php echo $a['name']; ?>" value="">
                        <?php echo $a['formTrail']; ?>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <script>
        document.addEventListener("DOMContentLoaded", function (event) {
            $(document).ready(function () {
                var jGui = $(".widget-listfilter-price");

                jGui.find(".slider-box").each(function () {

                    var jRange = $(this).find(".range-slider");
                    var jOk = $(this).find(".ok-box");
                    var jInput = $(this).find(".name-input");


                    var minVal = jRange.attr("data-min");
                    var maxVal = jRange.attr("data-max");
                    var currentMinVal = jRange.attr("data-current-min");
                    var currentMaxVal = jRange.attr("data-current-max");
                    var format = jRange.attr("data-format");


                    var hidden = true;
                    jRange.jRange({
                        from: minVal,
                        to: maxVal,
                        step: 1,
                        format: format,
                        width: 200,
                        showLabels: true,
                        isRange: true,
                        ondragend: function (v) {
                            if (true === hidden) {
                                jOk.removeClass("hidden");
                                hidden = false;
                            }
                            var values = v.split(',');
                            jInput.val(values[0] + '-' + values[1]);
                        }
                    });
                });
            });
        });
    </script>
<?php endif; ?>