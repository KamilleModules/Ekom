<?php

use Kamille\Utils\ThemeHelper\KamilleThemeHelper;
use Module\ThisApp\SokoForm\Renderer\ThisAppSokoTableFormRenderer;
use SokoForm\NotificationRenderer\SokoNotificationRenderer;
use Theme\LeeTheme;

KamilleThemeHelper::css("password-reset.css");
KamilleThemeHelper::css("table-form.css");
LeeTheme::useLib("soko");

ThisAppSokoTableFormRenderer::create()

?>
<div class="widget widget-reset-password">
    <h2>CHANGER MON MOT DE PASSE</h2>


    <?php if (true === $v['isSuccess']): ?>

    <?php SokoNotificationRenderer::create()->render($v['successNotif']); ?>
        <div class="account-link-container">
            <a href="<?php echo htmlspecialchars($v['uriDashboard']); ?>">Continuer vers mon compte</a>
        </div>


    <?php else: ?>

        <p>Associé à mon email {email}</p>


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
                        <label for="id-pass">Nouveau mot de passe</label>
                    </td>
                    <td>
                        <input name="{namePass}" type="password"
                               id="id-pass"
                               value="">
                    </td>
                </tr>

                <tr>
                    <td>
                        <label for="id-pass2">Confirmation du nouveau mot de passe</label>
                    </td>
                    <td>
                        <input name="{namePassConfirm}" type="password"
                               id="id-pass2"
                               value="">
                    </td>
                </tr>
                <tr class="submit-tr">
                    <td>
                        <button type="submit" class="lee-red-button">VALIDER</button>
                    </td>
                </tr>
            </table>
        </form>
    <?php endif; ?>
</div>