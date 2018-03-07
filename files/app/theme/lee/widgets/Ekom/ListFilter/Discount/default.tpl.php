<?php


use Module\ThisApp\Config\ThisAppViewHelper;
use Theme\Lee\Ekom\Helper\ColorsHelper;
use Theme\Lee\Ekom\Helper\ListFilterHelper;
use Theme\LeeTheme;

LeeTheme::useLib("jRange");


$badges = $v['badges'];
?>
<?php if ($badges): ?>

    <div
            id="widget-listfilter-discount"
            class="widget widget-listfilter-discount listfilter-box">

        <?php ListFilterHelper::renderListFilterTitle('Réductions'); ?>

        <div class="discount-boxes listfilter-body">
            <?php foreach ($badges as $badge):
                $sSel = (true === $badge['selected']) ? 'checked="checked"' : '';
                ?>


                <div
                        data-uri="<?php echo $badge['uri']; ?>"
                        class="location-box cb-toggler discount-box discount-trigger">
                    <div class="pretty <?php echo ThisAppViewHelper::getPrettyCheckboxFlavour(); ?> cb-toggler">
                        <input type="checkbox" <?php echo $sSel; ?> class=" cb-toggler"/>
                        <div class="state  cb-toggler">
                            <label class=" cb-toggler">
                                <a class="listfilter-link cb-toggler"
                                   href="<?php echo $badge['uri']; ?>"><?php echo $badge['label']; ?></a>
                            </label>
                        </div>
                    </div>
                </div>

            <?php endforeach; ?>
        </div>
    </div>

    <script>
        jqueryComponent.ready(function () {
            var jContext = $('#widget-listfilter-discount');
            jContext.on('click', function (e) {
                var jTarget = $(e.target);
                if (jTarget.hasClass("discount-trigger")) {
                    var uri = jTarget.closest(".discount-box").attr('data-uri');
                    window.location.href = uri;
                    return false;
                }
            });
        });
    </script>
<?php endif; ?>