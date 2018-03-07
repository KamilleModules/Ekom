<?php

use Module\ThisApp\SokoForm\Renderer\ThisAppSokoTableFormRenderer;
use Theme\LeeTheme;


LeeTheme::useLib("soko");


$r = ThisAppSokoTableFormRenderer::create()->setForm($v['form']);


?>
<div class="tool-form">
    <form class="soko-form soko-size-1" <?php $r->formAttributes(); ?>>
        <?php $r->notifications(); ?>
        <?php $r->title("MOT DE PASSE"); ?>
        <?php $r->submitKey(); ?>
        <table>
            <?php $r->render("current_pass", [
                "mandatory" => true,
                "style" => "radio",
            ]); ?>
            <?php $r->render("pass", [
                "mandatory" => true,
            ]); ?>
            <?php $r->render("pass_confirm", [
                "mandatory" => true,
            ]); ?>
        </table>
        <?php $r->autoRemovalScript(); ?>

        <?php $r->submitButton([
            'label' => "Mettre à jour le mot de passe",
        ]); ?>
    </form>
</div>