<?php

use Kamille\Utils\ThemeHelper\KamilleThemeHelper;
use Module\ThisApp\SokoForm\Renderer\ThisAppSokoTableFormRenderer;
use Theme\LeeTheme;

LeeTheme::useLib('soko');
KamilleThemeHelper::css("customer-all.css");


$r = ThisAppSokoTableFormRenderer::create()->setForm($v['form']);
?>

<div class="widget widget-customer-subscription tool-form">
    <form class="soko-form soko-size-1 h29" <?php $r->formAttributes(); ?>>
        <?php $r->notifications(); ?>
        <?php $r->title("MES ABONNEMENTS"); ?>
        <?php $r->submitKey(); ?>
        <table>
            <?php $r->render("abo_leader_mail"); ?>
            <?php $r->render("abo_leader_partners_mail"); ?>
            <?php $r->render("abo_leader_sms"); ?>

        </table>
        <?php $r->autoRemovalScript(); ?>

        <div class="social-container">
            <div class="bar-gray centered">SUIVEZ-NOUS</div>
            <div class="social-icons-bar">
                <a href="#" class="icon icon-facebook">&nbsp;</a>
                <a href="#" class="icon icon-twitter">&nbsp;</a>
                <a href="#" class="icon icon-instagram">&nbsp;</a>
                <a href="#" class="icon icon-pinterest">&nbsp;</a>
            </div>
        </div>
        <?php $r->submitButton([
            'label' => "Je valide",
        ]); ?>
    </form>

</div>



