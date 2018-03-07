<?php


use Kamille\Utils\ThemeHelper\KamilleThemeHelper;
use Module\Ekom\Utils\E;

KamilleThemeHelper::css("login.css");
KamilleThemeHelper::css("table-form.css");

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
                        <input name="<?php echo $v['nameEmail']; ?>" type="text"
                               value="<?php echo htmlspecialchars($v['valueEmail']); ?>"
                               placeholder="Votre email"
                        >
                    </td>
                </tr>
                <tr>
                    <td><input name="<?php echo $v['namePass']; ?>" type="password"
                               value="<?php echo htmlspecialchars($v['valuePass']); ?>"
                               placeholder="Mot de passe"
                        >
                    </td>
                </tr>
                <tr>
                    <td><input name="<?php echo $v['nameMemorize']; ?>" type="checkbox"
                               value="1"
                               id="memorize-input"
                            <?php echo $v['checkedMemorize']; ?>>
                        <label for="memorize-input" class="thin-text">
                            Mémoriser mes informations sur cet ordinateur
                        </label>
                    </td>
                </tr>
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
