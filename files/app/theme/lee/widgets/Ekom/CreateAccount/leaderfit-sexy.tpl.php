<?php


use Kamille\Utils\ThemeHelper\KamilleThemeHelper;

KamilleThemeHelper::css("tool-form.css");
KamilleThemeHelper::css("table-form.css");

?>
<div class="widget widget-create-account tool-form" style="padding-top: 50px;">
    <div class="title-bar">Créer un compte</div>
    <form class="table-form no-label" action="<?php echo $v['formAction']; ?>" method="<?php echo $v['formMethod']; ?>"
          style="width: 300px">
        <input type="hidden" name="{nameKey}" value="{valueKey}">
        <table>
            <?php if (($v['errorForm'])): ?>
                <tr>
                    <td>
                        <div class="error"><?php echo $v['errorForm']; ?></div>
                    </td>
                </tr>
            <?php endif; ?>
            <tr>
                <td>
                    <input name="{nameAccountType}" type="radio"
                           value="{valueAccountTypeB2C}"
                           id="account-type-b2c"
                           {checkedAccountTypeB2C}>
                    <label for="account-type-b2c">Particulier</label>
                    <input name="{nameAccountType}" type="radio"
                           value="{valueAccountTypeB2B}"
                           id="account-type-b2b"
                           {checkedAccountTypeB2B}>
                    <label for="account-type-b2b">Professionnel</label>
                </td>
            </tr>
            <tr>
                <td>
                    <input name="{nameName}" type="text"
                           value="{valueName}"
                           placeholder="Votre nom"
                    >
                </td>
            </tr>
            <tr>
                <td>
                    <input name="{nameEmail}" type="text"
                           value="{valueEmail}"
                           placeholder="E-mail"
                    >
                </td>
            </tr>
            <tr>
                <td><input name="{namePass}" type="password"
                           value="{valuePass}"
                           placeholder="Mot de passe, au moins 6 caractères"
                    >
                </td>
            </tr>
            <tr>
                <td><input name="{namePassConfirm}" type="password"
                           value="{valuePassConfirm}"
                           placeholder="Mot de passe à nouveau"
                    >
                </td>
            </tr>
            <tr class="submit-tr">
                <td>
                    <button type="submit" class="lee-red-button">Créer votre compte Leaderfit</button>
                </td>
            </tr>
        </table>
    </form>
</div>