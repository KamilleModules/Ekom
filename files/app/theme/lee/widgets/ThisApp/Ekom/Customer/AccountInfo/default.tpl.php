<?php

use FormTools\Rendering\FormToolsRenderer;
use FormTools\Validation\OnTheFlyFormValidator;
use Kamille\Utils\ThemeHelper\KamilleThemeHelper;
use Module\ThisApp\SokoForm\Renderer\ThisAppSokoTableFormRenderer;
use OnTheFlyForm\Helper\OnTheFlyFormHelper;
use Theme\LeeTheme;


KamilleThemeHelper::css("customer/update-account-info.css");
KamilleThemeHelper::js("phone-country-widget.js");

LeeTheme::useLib("soko");

$accountType = $v['accountType'];
$accountTypeLabel = ('b2b' === $accountType) ? 'Professionnel' : 'Particulier';


$r = ThisAppSokoTableFormRenderer::create()->setForm($v['form']);


?>
<div class="widget widget-account-info" id="widget-account-info">


    <div class="title">TYPE DE COMPTE: <span class="smaller"><?php echo $accountTypeLabel; ?></span></div>


    <!--    <div class="bar-red">MON COMPTE</div>-->
    <section>

        <form class="soko-form soko-size-1" <?php $r->formAttributes(); ?>>
            <?php $r->notifications(); ?>
            <?php $r->title("MES INFORMATIONS PERSONNELLES"); ?>
            <?php $r->submitKey(); ?>
            <table>
                <?php $r->render("gender", [
                    "mandatory" => true,
                    "style" => "radio",
                ]); ?>
                <?php $r->render("last_name", [
                    "mandatory" => true,
                ]); ?>
                <?php $r->render("first_name", [
                    "mandatory" => true,
                ]); ?>
                <?php $r->birthday("birthday", [
                    "mandatory" => true,
                ]); ?>
                <?php $r->phone("phone_prefix", "phone", [
                    "mandatory" => true,
                ]); ?>

            </table>

            <?php if ('b2b' === $v['accountType']): ?>

                <?php $r->title("MES INFORMATIONS PRO"); ?>
                <table>
                    <?php $r->render('b2b_company', [
                        "mandatory" => true,
                    ]); ?>
                    <?php $r->miniTitle("Pays de résidence de l'entreprise"); ?>
                    <?php $r->proCountry('user_country','b2b_siret', 'b2b_tva', [
                        'contextId' => "widget-account-info",
                    ]); ?>




                    <?php $r->miniTitle("Type de société"); ?>
                    <?php $r->renderAfterMiniTitle('pro_type', [
                        'style' => "radio",
                    ]); ?>
                    <?php $r->miniTitle("Secteur d'activité"); ?>
                    <?php $r->secteurPro('pro_secteur', "pro_secteur_autre", "pro_fonction"); ?>

                </table>


            <?php endif; ?>

            <?php $r->autoRemovalScript(); ?>

            <?php $r->submitButton([
                'label' => "Je valide",
            ]); ?>
        </form>
    </section>
</div>


<script>
    jqueryComponent.ready(function () {


        return false;

        var jWidget = $('#widget-account-info');

        function cleanAutreChoice() {
            jWidget.find('.ei-secteur-autre-input').val("");
        }


        jWidget.on('click', function (e) {
            var jTarget = $(e.target);
            if (jTarget.hasClass("ei-secteur-autre-input")) {
                $("#ei-secteur-autre").prop('checked', true);
                return false;
            }
            else if (jTarget.hasClass("ei-secteur-not-input")) {
                cleanAutreChoice();
            }
        });


        jWidget.find('.ei-secteur-not-input').on('focus', function () {
            cleanAutreChoice();
        });


    });
</script>

