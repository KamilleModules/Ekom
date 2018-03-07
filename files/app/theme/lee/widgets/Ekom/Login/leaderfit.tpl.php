<?php


use Kamille\Utils\ThemeHelper\KamilleThemeHelper;
use Module\Ekom\Utils\E;
use Module\ThisApp\SokoForm\Renderer\ThisAppSokoTableFormRenderer;
use SokoForm\Renderer\SokoFormRenderer;
use SokoForm\Renderer\SokoTableFormRenderer;

KamilleThemeHelper::css("login.css");
KamilleThemeHelper::css("table-form.css");


$r = ThisAppSokoTableFormRenderer::create()->setForm($v);
?>
<div class="widget widget-login window">


    <div class="left-column t-raleway">
        <h2>PREMIÈRE VISITE ?</h2>
        <p class="text">
            En quelques secondes, créez votre espace client
        </p>
        <a href="{uriCreateAccount}" class="lee-red-button">CRÉER MON COMPTE</a>
    </div>
    <div class="right-column">
        <h2>DÉJÀ UN COMPTE ?</h2>
        <p class="text">
            Connectez-vous avec votre e-mail et mot de passe
        </p>
        <form class="table-form no-label"
              <?php $r->formAttributes(); ?>
              style="width: 300px">
            <?php $r->submitKey() ; ?>
            <?php $r->notifications(); ?>


            <table>
                <?php $r->render('email'); ?>
                <?php $r->render('pass'); ?>
                <?php $r->render('memorize', [
                        'attributes' => [
                                "class" => "thin-text",
                        ],
                        'style' => 'checkbox',
                ]); ?>
                <tr class="submit-tr">
                    <td>
                        <button type="submit" class="lee-red-button">SE CONNECTER</button>
                    </td>
                </tr>
                <tr class="submit-tr">
                    <td>
                        <a class="thin-text" href="{uriForgotPassword}">Mot de passe oublié</a>
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>
