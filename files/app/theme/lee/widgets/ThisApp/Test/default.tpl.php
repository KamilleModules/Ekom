<?php

use Kamille\Utils\ThemeHelper\KamilleThemeHelper;
use OnTheFlyForm\Helper\OnTheFlyFormHelper;
use Theme\LeeTheme;


LeeTheme::useLib("onTheFlyForm");
KamilleThemeHelper::css("table-form.css");
$m = $v['formModel'];


?>
<form action="" method="post" style="width: 500px" class="table-form" id="context">
    <?php if (true === $m['isPosted']): ?>
        <?php if ('' !== $m['successMessage']): ?>
            <p class="off-success-message">Success message</p>
        <?php elseif ('' !== $m['errorMessage']): ?>
            <p class="off-error-message">Error message</p>
        <?php endif; ?>
    <?php endif; ?>


    <?php OnTheFlyFormHelper::generateKey($m); ?>


    <table>
        <tr>
            <td>Prénom</td>
            <td>
                <input name="{m:nameFirstName}" type="text"
                       value="{m:valueFirstName}">

            </td>
        </tr>
        <?php if ('' !== $m['errorFirstName']): ?>
            <tr data-error="{m:nameFirstName}">
                <td></td>
                <td data-error-text="1" class="error">
                    {m:errorFirstName}
                </td>
            </tr>
        <?php endif; ?>
        <tr>
            <td>Nom</td>
            <td><input name="{m:nameLastName}" type="text"
                       value="{m:valueLastName}">
            </td>
        </tr>
        <?php if ('' !== $m['errorLastName']): ?>
            <tr data-error="{m:nameLastName}">
                <td></td>
                <td data-error-text="1" class="error">
                    {m:errorLastName}
                </td>
            </tr>
        <?php endif; ?>
        <tr>
            <td>Adresse</td>
            <td><input name="{m:nameAddress}" type="text"
                       value="{m:valueAddress}">
            </td>
        </tr>
        <?php if ('' !== $m['errorAddress']): ?>
            <tr data-error="{m:nameAddress}">
                <td></td>
                <td data-error-text="1" class="error">
                    {m:errorAddress}
                </td>
            </tr>
        <?php endif; ?>
        <tr>
            <td>Code postal</td>
            <td><input name="{m:namePostcode}" type="text"
                       value="{m:valuePostcode}">
            </td>
        </tr>
        <?php if ('' !== $m['errorPostcode']): ?>
            <tr data-error="{m:namePostcode}">
                <td></td>
                <td data-error-text="1" class="error">
                    {m:errorPostcode}
                </td>
            </tr>
        <?php endif; ?>
        <tr>
            <td>Ville</td>
            <td><input name="{m:nameCity}" type="text"
                       value="{m:valueCity}">
            </td>
        </tr>
        <?php if ('' !== $m['errorCity']): ?>
            <tr data-error="{m:nameCity}">
                <td></td>
                <td data-error-text="1" class="error">
                    {m:errorCity}
                </td>
            </tr>
        <?php endif; ?>
        <tr>
            <td>Pays</td>
            <td><select name="{m:nameCountry}"
                >
                    <?php OnTheFlyFormHelper::selectOptions($m['optionsCountry'], $m['valueCountry']); ?>
                </select>
            </td>
        </tr>
        <?php if ('' !== $m['errorCountry']): ?>
            <tr data-error="{m:nameCountry}">
                <td></td>
                <td data-error-text="1" class="error">
                    {m:errorCountry}
                </td>
            </tr>
        <?php endif; ?>
        <tr>
            <td>Numéro de téléphone</td>
            <td><input name="{m:namePhone}" type="text"
                       value="{m:valuePhone}">
            </td>
        </tr>
        <?php if ('' !== $m['errorPhone']): ?>
            <tr data-error="{m:namePhone}">
                <td></td>
                <td data-error-text="1" class="error">
                    {m:errorPhone}
                </td>
            </tr>
        <?php endif; ?>
        <tr>
            <td>
                <span data-tip="Peut être imprimé sur l'étiquette pour faciliter la livraison (par exemple le code d'accès de la résidence)."
                      class="hint">Informations complémentaires</span>
            </td>
            <td><input name="{m:nameSupplement}" type="text"
                       value="{m:valueSupplement}"></td>
        </tr>
    </table>
    <div class="table-form-bottom">
        <button class="submit-btn create-new-address-btn">Créer cette adresse</button>
        <button>Annuler</button>
    </div>
</form>

<script>
    document.addEventListener("DOMContentLoaded", function (event) {
        $(document).ready(function () {
            var jContext = $('#context');
            window.onTheFlyForm.staticFormInit(jContext);
        });
    });
</script>