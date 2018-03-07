<?php

use FormTools\Rendering\FormToolsRenderer;
use FormTools\Validation\OnTheFlyFormValidator;
use Kamille\Utils\ThemeHelper\KamilleThemeHelper;
use OnTheFlyForm\Helper\OnTheFlyFormHelper;
use Theme\LeeTheme;

KamilleThemeHelper::css("customer-all.css");
KamilleThemeHelper::css("customer/training-history.css");
LeeTheme::useLib("collapsibleBar");


?>

<div class="widget widget-training-history" id="widget-training-history">
    <div class="bar-red">MON DOSSIER DE FORMATION</div>
    <div class="container">

        <?php foreach ($v['items'] as $m):
            $sState = "";
            ?>
            <div class="training-item <?php echo $sState; ?> collapsible-parent">
                <div class="bar-gray collapsible-bar collapse-trigger <?php echo $sState; ?>">
                    <div class="training-name collapse-trigger"><?php echo $m['label']; ?></div>
                    <div class="training-session collapse-trigger"><?php echo $m['training_date']; ?></div>
                    <div class="trigger collapse-trigger"></div>
                </div>
                <div class="content">
                    <div class="info">
                        <div class="line">N° de commande: <?php echo $m['ref']; ?></div>
                        <div class="line">Date d'achat: <?php echo date('d/m/Y', strtotime($m['date'])); ?></div>
                        <div class="line">Session: <?php echo $m['label']; ?>-<?php echo $m['location']; ?></div>
                        <div class="line">Date de session: <?php echo $m['training_date']; ?></div>
                        <div class="line">Montant: <?php echo $m['productTotal']; ?></div>
                    </div>
                    <div class="downloads">
                        <div class="download-item bus-block">
                            <div class="title-block">
                                <div class="title">DEMANDE DE PARTICIPATION</div>
                            </div>
                            <a class="bottom" href="<?php echo $m['uriParticipation']; ?>">TÉLÉCHARGER <span
                                        class="icon-download"></span></a>
                        </div>
                        <div class="download-item bus-block">
                            <div class="title-block">
                                <div class="title">MON CONTRAT</div>
                            </div>
                            <a class="bottom" href="<?php echo $m['uriContrat']; ?>">TÉLÉCHARGER <span
                                        class="icon-download"></span></a>
                        </div>
                        <div class="download-item bus-block">
                            <div class="title-block">
                                <div class="title">CONVOCATION</div>
                            </div>
                            <a class="bottom" href="<?php echo $m['uriConvocation']; ?>">TÉLÉCHARGER <span
                                        class="icon-download"></span></a>
                        </div>
                        <div class="download-item bus-block">
                            <div class="title-block">
                                <div class="title">ATTESTATION DE PRÉSENCE</div>
                            </div>
                            <a class="bottom" href="<?php echo $m['uriPresence']; ?>">TÉLÉCHARGER <span
                                        class="icon-download"></span></a>
                        </div>
                        <div class="download-item bus-block">
                            <div class="title-block">
                                <div class="title">FEUILLE D'ÉMARGEMENT</div>
                            </div>
                            <a class="bottom" href="<?php echo $m['uriEmargement']; ?>">TÉLÉCHARGER <span
                                        class="icon-download"></span></a>
                        </div>
                        <div class="download-item bus-block">
                            <div class="title-block">
                                <div class="title">ATTESTATION DE RÉUSSITE</div>
                            </div>
                            <a class="bottom" href="<?php echo $m['uriReussite']; ?>">TÉLÉCHARGER <span
                                        class="icon-download"></span></a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

</div>


<script>
    document.addEventListener("DOMContentLoaded", function (event) {
        $(document).ready(function () {
            var jWidget = $("#widget-training-history");
            jWidget.collapsibleBar();
        });
    });
</script>