<?php


use Module\ThisApp\Config\ThisAppViewHelper;
use Theme\Lee\Ekom\Helper\ColorsHelper;
use Theme\Lee\Ekom\Helper\ListFilterHelper;
use Theme\LeeTheme;

LeeTheme::useLib("prettyCheckbox");

$filterBoxes = $v['filterBoxes'];


?>
<div class="widget widget-listfilter-attributes widget-listfilter">

    <?php foreach ($filterBoxes as $name => $a):

        ?>
        <div class="attribute-box listfilter-box">

            <?php ListFilterHelper::renderListFilterTitle($a['title']); ?>

            <?php if ('couleur' === $name): ?>

                <div class="attribute-links attribute-color-links listfilter-body">
                    <?php foreach ($a['items'] as $item): ?>
                        <span data-uri="<?php echo $item['uri']; ?>" class="color color-attr"
                              style="background: <?php echo ColorsHelper::getColorLabel($item['value']); ?>;"><?php echo $item['label']; ?></span>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>

                <?php
                $type = $a['type'];
                switch ($type):
                    default:

                        ?>
                        <div class="attribute-links listfilter-body">
                            <?php
                            foreach ($a['items'] as $item):
                                $sSel = (true === $item['selected']) ? ' checked="checked"' : '';
                                ?>



                                <div class="location-box cb-toggler">
                                    <div class="pretty <?php echo ThisAppViewHelper::getPrettyCheckboxFlavour(); ?> cb-toggler">
                                        <input type="checkbox" <?php echo $sSel; ?> class=" cb-toggler"/>
                                        <div class="state  cb-toggler">
                                            <label class=" cb-toggler">
                                                <a class="listfilter-link cb-toggler"
                                                   href="<?php echo $item['uri']; ?>"><?php echo $item['label']; ?></a>
                                            </label>
                                        </div>
                                    </div>
                                </div>


                            <?php endforeach; ?>
                        </div>
                        <?php break; ?>
                    <?php endswitch; ?>

            <?php endif; ?>

        </div>
    <?php endforeach; ?>
</div>


<script>
    document.addEventListener("DOMContentLoaded", function (event) {
        $(document).ready(function () {
            var jGui = $(".widget-listfilter-attributes");
            jGui.find(".c-box").on('click', function () {
                $(this).parent().find('a')[0].click(); // might not work in safari? https://stackoverflow.com/questions/20928915/jquery-triggerclick-not-working
            });


            jGui.on('click', function (e) {
                var jTarget = $(e.target);
                if (jTarget.hasClass("color-attr")) {
                    var uri = jTarget.attr('data-uri');
                    window.location.href = uri;

                }
            });
        });
    });
</script>