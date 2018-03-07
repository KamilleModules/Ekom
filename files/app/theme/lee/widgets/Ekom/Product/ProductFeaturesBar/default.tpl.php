<?php

use Kamille\Utils\ThemeHelper\KamilleThemeHelper;
use Module\ThisApp\Helper\ThisAppHelper;

KamilleThemeHelper::css("product-features-bar.css");


$sStyle = "";
if (false === $v['hasRelatedProducts']) {
    $sStyle = 'style="margin-top:30px;"';
}


$isTraining = ThisAppHelper::isTraining($v['seller']);
$hasEvent = false;

$hasEvent = (array_key_exists('hasEvents', $v) && true === $v['hasEvents']);


$textFeatures = "FICHE TECHNIQUE";
if ($isTraining) {
    $textFeatures = "+ D'INFOS";
}

?>
<div class="product-features-bar widget widget-product-feature-bar" id="widget-product-feature-bar"
    <?php echo $sStyle; ?>>
    <div class="window">
        <ul>

            <?php if (true === $hasEvent): ?>
                <li class="trigger">
                    <a class="trigger" href="#widget-product-events-agenda">PLANNING</a>
                </li>
                <li class="trigger">
                    <a class="trigger" href="#widget-product-events-presenters">PRESENTERS</a>
                </li>
                <li class="trigger">
                    <a class="trigger" href="#widget-product-events-location">LIEU</a>
                </li>
                <li class="trigger">
                    <a class="trigger" href="#widget-product-events-hotel">HÔTEL</a>
                </li>
            <?php else: ?>
                <li class="trigger">
                    <a class="trigger" href="#widget-product-features"><?php echo $textFeatures; ?></a>
                </li>
            <?php endif; ?>
            <?php if (true === $isTraining): ?>
                <li class="trigger">
                    <a class="trigger" href="#widget-product-trainers">FORMATEURS</a>
                </li>
            <?php endif; ?>
            <?php if (true === $v['hasBundle']): ?>
                <li class="trigger">
                    <a class="trigger" href="#widget-product-bundles">PACKS</a>
                </li>
            <?php endif; ?>
            <li class="trigger">
                <a class="trigger" href="#widget-product-comments">AVIS
                    <?php if ($v['nbComments']): ?>
                        (<?php echo $v['nbComments']; ?>)
                    <?php endif; ?>
                </a>
            </li>

            <?php if (true === $v['hasRelatedTrainings']): ?>
                <li class="trigger">
                    <a class="trigger" href="#widget-related-training">FORMATION</a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</div>


<script>

        jqueryComponent.ready(function () {


            var api = ekomApi.inst();
            var jWidget = $('#widget-product-feature-bar');


            api.on('scrollToFeatureBarItem', function (hash) {


                jWidget.find('li').each(function () {
                    var liHash = $(this).find('a').attr('href');
                    if (liHash === hash) {
                        $(this).addClass("active");
                    }
                    else {
                        $(this).removeClass("active");
                    }
                });


                var number = $(hash).offset().top;
                number -= 50;

                $('html, body').animate({
                    scrollTop: number
                }, 400);
            });


            jWidget.on("click", function (e) {
                var jTarget = $(e.target);
                if (jTarget.hasClass('trigger')) {

                    var jLi = jTarget.closest('li');
                    var hash = jLi.find("a").attr("href");
                    api.trigger("scrollToFeatureBarItem", hash);


                    return false;
                }
            });
        });
</script>