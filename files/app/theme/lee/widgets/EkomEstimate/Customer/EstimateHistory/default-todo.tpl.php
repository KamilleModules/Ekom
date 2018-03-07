<?php

use FormTools\Rendering\FormToolsRenderer;
use FormTools\Validation\OnTheFlyFormValidator;
use Kamille\Utils\ThemeHelper\KamilleThemeHelper;
use OnTheFlyForm\Helper\OnTheFlyFormHelper;
use Theme\LeeTheme;

KamilleThemeHelper::css("customer-all.css");
KamilleThemeHelper::css("customer/estimate-history.css");


$items = $v['items'];

?>

<div class="widget widget-estimate-history" id="widget-estimate-history">
    <div class="bar-red">MES DEVIS</div>
    <div class="container">

        <?php foreach ($items as $m):
            $sState = ""; // open | <emptyString>
            ?>
            <div class="estimate-item <?php echo $sState; ?>">
                <div class="bar-gray collapsible-bar collapse-trigger <?php echo $sState; ?>">
                    <div class="estimate-date collapse-trigger">DEVIS DU <?php echo $m['date']; ?></div>
                    <div class="estimate-price collapse-trigger"><?php echo $m['price']; ?></div>
                    <div class="trigger collapse-trigger"></div>
                </div>
                <div class="content">
                    <?php foreach ($m['items'] as $n): ?>
                        <div class="estimate-element bus-block">
                            <div class="facture">DEVIS</div>
                            <div class="lines">
                                <div class="line">
                                    <span class="label">N° de devis:</span>
                                    <span class="value small"><?php echo $n['ref']; ?></span>
                                </div>
                                <div class="line">
                                    <span class="label">Date d'achat:</span>
                                    <span class="value"><?php echo date("d/m/Y", strtotime($n['date'])); ?></span>
                                </div>
                                <div class="line">
                                    <span class="value"><?php echo $n['seller']; ?></span>
                                </div>
                                <div class="line">
                                    <span class="label">Montant:</span>
                                    <span class="value"><?php echo $n['orderGrandTotal']; ?></span>
                                </div>
                            </div>

                            <a href="#" class="bottom">
                                TÉLÉCHARGER
                                <span class="icon-download">&nbsp;</span>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

</div>


<script>
    document.addEventListener("DOMContentLoaded", function (event) {
        $(document).ready(function () {
            var jWidget = $("#widget-estimate-history");
            jWidget.on('click', function (e) {
                var jTarget = $(e.target);
                if (jTarget.hasClass("collapse-trigger")) {
                    var jBar = jTarget.closest(".collapsible-bar");
                    var jItem = jBar.closest(".estimate-item");
                    var jContent = jItem.find("> .content");
                    if (jItem.hasClass("open")) {
//                        jContent.show();
                        jItem.removeClass("open");
                        jBar.removeClass("open");
                    }
                    else {
//                        jContent.hide();
                        jItem.addClass("open");
                        jBar.addClass("open");
                    }
                    return false;
                }
            });
        });
    });
</script>