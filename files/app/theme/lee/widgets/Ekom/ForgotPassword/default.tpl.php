<?php

use Kamille\Utils\ThemeHelper\KamilleThemeHelper;

KamilleThemeHelper::css("password-recovery.css");
KamilleThemeHelper::css("table-form.css");

?>
<div class="widget widget-password-recovery">
    <h2>MOT DE PASSE OUBLIÉ ?</h2>


    <p>Saisissez ci-dessous le mail de votre compte client puis cliquez sur valider.</p>
    <p class="small">Vous recevrez ensuite par mail un lien vous permettant de créer un nouveau mot de passe en
        toute
        simplicité.</p>

    <?php if (($v['errorForm'])): ?>
        <div class="error">
            <p><?php echo $v['errorForm']; ?></p>
        </div>
    <?php endif; ?>

    <form class="table-form no-label" action="<?php echo $v['formAction']; ?>"
          method="<?php echo $v['formMethod']; ?>">
        <input type="hidden" name="{nameKey}" value="{valueKey}">


        <table>

            <tr>
                <td>
                    <input name="<?php echo $v['nameEmail']; ?>" type="text"
                           placeholder="Votre email"
                           value="<?php echo htmlspecialchars($v['valueEmail']); ?>">
                </td>
            </tr>
            <tr class="submit-tr">
                <td>
                    <button type="submit" class="lee-red-button">VALIDER</button>
                </td>
            </tr>
        </table>


    </form>
</div>