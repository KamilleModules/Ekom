<?php

use Kamille\Mvc\HtmlPageHelper\HtmlPageHelper;
use Module\Ekom\Utils\E;
use Module\ThisApp\SokoForm\Renderer\ThisAppSokoTableFormRenderer;
use Theme\LeeTheme;


LeeTheme::useLib("soko");


$r = ThisAppSokoTableFormRenderer::create()->setForm($v['form']);
$lang = E::getLangIso();

$accountTypeValue = $r->getControlProperty("account_type", "value");
HtmlPageHelper::js("https://www.google.com/recaptcha/api.js");


?>
<div
        id="widget-create-account"
        class="widget widget-create-account soko-form mauto soko-size-1 container-<?php echo $accountTypeValue; ?>"
>


    <form <?php $r->formAttributes(); ?>>
        <?php $r->submitKey(); ?>
        <?php $r->notifications(); ?>



        <?php $r->formTopSwitcher('account_type'); ?>


        <div class="b2c-bloc">


            <?php $r->title("MES INFORMATIONS DE COMPTE"); ?>
            <table>
                <?php $r->render('email', [
                    "mandatory" => true,
                ]); ?>
                <?php $r->render('password', [
                    "mandatory" => true,
                ]); ?>
                <?php $r->render('password2', [
                    "mandatory" => true,
                ]); ?>
            </table>

            <?php $r->title("MES INFORMATIONS PERSONNELLES"); ?>
            <table>
                <?php $r->render('gender', [
                    'style' => "radio",
                ]); ?>
                <?php $r->render('last_name', [
                    "mandatory" => true,
                ]); ?>
                <?php $r->render('first_name', [
                    "mandatory" => true,
                ]); ?>
                <?php $r->datePicker('birthday_date', [
                    'lang' => $lang,
                    'mandatory' => true,
                    "sokoFormName" => "createAccount",
                ]); ?>
                <?php $r->phone('phone_prefix', 'phone', [
                    "mandatory" => true,
                ]); ?>

                <?php $r->miniTitle("Pays de résidence"); ?>
                <?php $r->renderAfterMiniTitle('user_country', [
                    'label' => "Pays de résidence",
                ]); ?>
            </table>


            <?php $r->title("MES ABONNEMENTS"); ?>
            <table>

                <?php $r->render('abos'); ?>

            </table>
            <?php $r->separator(); ?>
            <table>
                <?php $r->render('i_agree'); ?>
            </table>
        </div>

        <div class="b2b-bloc">
            <?php $r->title("MES INFORMATIONS DE COMPTE"); ?>
            <table>
                <?php $r->render('email_b2b', [
                    "mandatory" => true,
                ]); ?>
                <?php $r->render('password_b2b', [
                    "mandatory" => true,
                ]); ?>
                <?php $r->render('password2_b2b', [
                    "mandatory" => true,
                ]); ?>
            </table>


            <?php $r->title("MES INFORMATIONS PRO"); ?>
            <table>
                <?php $r->render('company', [
                    "mandatory" => true,
                ]); ?>
                <?php $r->miniTitle("Pays de résidence de l'entreprise"); ?>
                <?php $r->proCountry('user_country_b2b', 'siret', 'tva', [
                    'contextId' => "widget-create-account",
                ]); ?>


                <?php $r->miniTitle("Type de société"); ?>
                <?php $r->renderAfterMiniTitle('ei_type', [
                    'style' => "radio",
                ]); ?>
                <?php $r->miniTitle("Secteur d'activité"); ?>
                <?php $r->secteurPro('ei_secteur', "ei_secteur_input", "ei_fonction"); ?>

            </table>
            <?php $r->title("MES INFORMATIONS PERSONNELLES"); ?>
            <table>
                <?php $r->render('gender_b2b', [
                    'style' => "radio",
                ]); ?>
                <?php $r->render('last_name_b2b', [
                    "mandatory" => true,
                ]); ?>
                <?php $r->render('first_name_b2b', [
                    "mandatory" => true,
                ]); ?>
                <?php $r->datePicker('birthday_date_b2b', [
                    'lang' => $lang,
                    "mandatory" => true,
                    "sokoFormName" => "createAccount",
                ]); ?>
                <?php $r->phone('phone_prefix_b2b', 'phone_b2b', [
                    "mandatory" => true,
                ]); ?>
            </table>


            <?php $r->title("MES ABONNEMENTS"); ?>
            <table>
                <?php $r->render('abos_b2b'); ?>

            </table>

            <?php $r->title("SUIVEZ-NOUS"); ?>
            <?php $r->socialIcons(); ?>

            <?php $r->separator(); ?>
            <table>
                <?php $r->render('i_agree_b2b'); ?>
            </table>


        </div>
        <div class="g-recaptcha" data-sitekey="<?php echo $v['recaptchaSiteKey']; ?>"></div>
        <?php $r->submitButton([
            'label' => "Je valide",
            'attributes' => [
                'style' => "margin-top: 20px",
            ],
        ]); ?>
    </form>


</div>
<?php $r->autoRemovalScript(); ?>
