<?php

use Kamille\Utils\ThemeHelper\KamilleThemeHelper;
use OnTheFlyForm\Helper\OnTheFlyFormHelper;
use Theme\LeeTheme;


LeeTheme::useLib("onTheFlyForm");
KamilleThemeHelper::css("tool-form.css");
KamilleThemeHelper::css("table-form.css");
$m = $v['formModel'];


?>
<div class="tool-form">
    <form action="" method="post" style="width: 500px" class="table-form"
          id="user-account-credentials-change-password-form">

        <?php if (true === $m['isPosted']): ?>
            <?php if ('' !== $m['successMessage']): ?>
                <p class="off-success-message">{m:successMessage}</p>
            <?php elseif ('' !== $m['errorMessage']): ?>
                <p class="off-error-message">{m:errorMessage}</p>
            <?php endif; ?>
        <?php endif; ?>

        <?php OnTheFlyFormHelper::generateKey($m); ?>


        <div class="title-bar">MOT DE PASSE</div>

        <table>
            <tr>
                <td>Mot de passe actuel</td>
                <td>
                    <input name="{m:nameCurrentPass}" type="password"
                           value="{m:valueCurrentPass}">

                </td>
            </tr>
            <?php if ('' !== $m['errorCurrentPass']): ?>
                <tr data-error="{m:nameCurrentPass}">
                    <td></td>
                    <td data-error-text="1" class="error">
                        {m:errorCurrentPass}
                    </td>
                </tr>
            <?php endif; ?>
            <tr>
                <td>Nouveau mot de passe</td>
                <td>
                    <input name="{m:namePass}" type="password"
                           value="{m:valuePass}">

                </td>
            </tr>
            <?php if ('' !== $m['errorPass']): ?>
                <tr data-error="{m:namePass}">
                    <td></td>
                    <td data-error-text="1" class="error">
                        {m:errorPass}
                    </td>
                </tr>
            <?php endif; ?>
            <tr>
                <td>Nouveau mot de passe (encore une fois)</td>
                <td>
                    <input name="{m:namePassConfirm}" type="password"
                           value="{m:valuePassConfirm}">

                </td>
            </tr>
            <?php if ('' !== $m['errorPassConfirm']): ?>
                <tr data-error="{m:namePassConfirm}">
                    <td></td>
                    <td data-error-text="1" class="error">
                        {m:errorPassConfirm}
                    </td>
                </tr>
            <?php endif; ?>
        </table>
        <div class="table-form-bottom">
            <button class="lee-red-button update-password-btn">Mettre à jour le mot de passe</button>
        </div>
    </form>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function (event) {
        $(document).ready(function () {
            var jContext = $('#user-account-credentials-change-password-form');
            window.onTheFlyForm.formInit(jContext);
        });
    });
</script>