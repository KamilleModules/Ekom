<?php

use FormTools\Rendering\FormToolsRenderer;
use FormTools\Validation\OnTheFlyFormValidator;
use Kamille\Utils\ThemeHelper\KamilleThemeHelper;
use OnTheFlyForm\Helper\OnTheFlyFormHelper;
use Theme\LeeTheme;

KamilleThemeHelper::css("customer-all.css");

KamilleThemeHelper::css("tool-form.css");
KamilleThemeHelper::css("table-form.css");
KamilleThemeHelper::css("customer/update-account-info.css");
KamilleThemeHelper::js("phone-country-widget.js");

LeeTheme::useLib("onTheFlyForm");

$accountType = $v['accountType'];
$accountTypeLabel = ('b2b' === $accountType) ? 'Professionnel' : 'Particulier';

$m = $v['formModel'];


$phoneCountries = $m['phoneCountries'];


?>

<div class="widget widget-account-info tool-form" id="widget-account-info">


    <?php if (true === $m['isPosted']): ?>
        <?php if ('' !== $m['successMessage']): ?>
            <p class="off-success-message form-handling-success">{m:successMessage}</p>
        <?php elseif ('' !== $m['errorMessage']): ?>
            <p class="off-error-message form-handling-error">{m:errorMessage}</p>
        <?php endif; ?>
    <?php endif; ?>


    <div class="title">TYPE DE COMPTE: <span class="smaller"><?php echo $accountTypeLabel; ?></span></div>


    <!--    <div class="bar-red">MON COMPTE</div>-->
    <section>
        <form class="table-form no-label" action="{m:formAction}"
              method="{m:formMethod}"

        >
            <div class="title-bar">MES INFORMATIONS PERSONNELLES</div>

            <?php OnTheFlyFormHelper::generateKey($m); ?>


            <table>
                <tr>
                    <td>
                        <div class="radio-inputs">
                            <input name="{m:nameGender}" type="radio"
                                   value="{m:valueGender__Female}"
                                   id="gender-female"
                                   {m:checkedGender__Female}>
                            <label for="gender-female">Madame</label>
                            <input name="{m:nameGender}" type="radio"
                                   value="{m:valueGender__Male}"
                                   id="gender-male"
                                   {m:checkedGender__Male}>
                            <label for="gender-male">Monsieur</label>
                        </div>
                    </td>
                </tr>
                <?php if ('' !== $m['errorGender']): ?>
                    <tr data-error="{m:nameGender}">
                        <td>
                            <p data-error-text="1" class="error">{m:errorGender}</p>
                        </td>
                    </tr>
                <?php endif; ?>
                <tr>
                    <td>
                        <div class="control">
                            <span class="asterisk">*</span>
                            <input name="{m:nameLastName}" type="text"
                                   value="{m:valueLastName}"
                                   placeholder="Nom"
                            >
                        </div>
                    </td>
                </tr>
                <?php if ('' !== $m['errorLastName']): ?>
                    <tr data-error="{m:nameLastName}">
                        <td>
                            <p data-error-text="1" class="error">{m:errorLastName}</p>
                        </td>
                    </tr>
                <?php endif; ?>
                <tr>
                    <td>
                        <div class="control">
                            <span class="asterisk">*</span>
                            <input name="{m:nameFirstName}" type="text"
                                   value="{m:valueFirstName}"
                                   placeholder="Prénom"
                            >
                        </div>
                    </td>
                </tr>
                <?php if ('' !== $m['errorFirstName']): ?>
                    <tr data-error="{m:nameFirstName}">
                        <td>
                            <p data-error-text="1" class="error">{m:errorLastName}</p>
                        </td>
                    </tr>
                <?php endif; ?>
                <tr>
                    <td>
                        <label class="birthday">Date de naissance</label>
                        <select name="{m:nameBirthdayDay}">
                            <?php FormToolsRenderer::selectOptions($m['optionsBirthdayDay'], $m["valueBirthdayDay"]); ?>
                        </select>
                        <select name="{m:nameBirthdayMonth}">
                            <?php FormToolsRenderer::selectOptions($m['optionsBirthdayMonth'], $m["valueBirthdayMonth"]); ?>
                        </select>
                        <select name="{m:nameBirthdayYear}">
                            <?php FormToolsRenderer::selectOptions($m['optionsBirthdayYear'], $m["valueBirthdayYear"]); ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="phone-line-td" style="position: relative">

                        <input class="phone-prefix-input" type="hidden" name="{m:namePhonePrefix}"
                               value="{m:valuePhonePrefix}">

                        <div class="phone-line phone-selector-trigger">
                            <div class="phone-prefix-selector-trigger phone-selector-trigger">
                                <div class="phone-flag flag flag-{m:phoneCountry} phone-selector-trigger"></div>
                                <div class="phone-prefix phone-selector-trigger">+{m:valuePhonePrefix}</div>
                            </div>
                            <input name="{m:namePhone}" type="text"
                                   value="{m:valuePhone}"
                                   placeholder="Téléphone"
                            >
                        </div>

                        <div class="phone-prefix-selector">
                            <ul>
                                <?php foreach ($phoneCountries as $c): ?>
                                    <li data-prefix="<?php echo $c['prefix']; ?>"
                                        data-country="<?php echo $c['country']; ?>" class="phone-choice">
                                        <div class="flag flag-<?php echo $c['country']; ?> phone-choice"></div>
                                        <div class="phone phone-choice">+<?php echo $c['prefix']; ?></div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </td>
                </tr>
            </table>


            <?php if ('b2b' === $v['accountType']): ?>

                <div class="title-bar">MES INFORMATIONS PRO</div>
                <table>
                    <tr>
                        <td>
                            <div class="control">
                                <span class="asterisk">*</span>
                                <input name="{m:nameCompany}" type="text"
                                       value="{m:valueCompany}"
                                       data-input-id="company"
                                       data-error-popout="company"
                                       placeholder="Raison sociale"
                                >
                            </div>
                        </td>
                    </tr>
                    <tr data-error="company">
                        <td>
                            <p class="error" data-error-text="1">{m:errorCompany}</p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="control">
                                <span class="asterisk">*</span>
                                <input name="{m:nameSiret}" type="text"
                                       value="{m:valueSiret}"
                                       data-input-id="siret"
                                       data-error-popout="siret"
                                       placeholder="N° SIRET"
                                >
                            </div>
                        </td>
                    </tr>
                    <tr data-error="siret">
                        <td>
                            <p class="error" data-error-text="1">{m:errorSiret}</p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="control">
                                <span class="asterisk">*</span>
                                <input name="{m:nameTva}" type="text"
                                       value="{m:valueTva}"
                                       data-input-id="tva"
                                       data-error-popout="tva"
                                       placeholder="TVA intracommunautaire"
                                >
                            </div>
                        </td>
                    </tr>
                    <tr data-error="tva">
                        <td>
                            <p class="error" data-error-text="1">{m:errorTva}</p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="pro-extra-info">
                                <div class="bloc">
                                    <div class="title-label">Type de société</div>
                                    <div class="radio-inputs">
                                        <input name="{m:nameEiType}" type="radio"
                                               value="{m:valueEiType__Entreprise}"
                                               id="ei-type-entreprise"
                                               {m:checkedEiType__Entreprise}>
                                        <label for="ei-type-entreprise">Entreprise</label>
                                        <input name="{m:nameEiType}" type="radio"
                                               value="{m:valueEiType__Association}"
                                               id="ei-type-association"
                                               {m:checkedEiType__Association}>
                                        <label for="ei-type-association">Association</label>

                                        <input name="{m:nameEiType}" type="radio"
                                               value="{m:valueEiType__Independant}"
                                               id="ei-type-independant"
                                               {m:checkedEiType__Independant}>
                                        <label for="ei-type-independant">Independant</label>
                                    </div>
                                </div>
                                <div class="bloc">
                                    <div class="title-label">Secteur d'activité</div>
                                    <div class="radio-inputs">
                                        <input name="{m:nameEiSecteur}" type="radio"
                                               value="{m:valueEiSecteur__Forme}"
                                               id="ei-secteur-forme"
                                               class="ei-secteur-not-input"
                                               {m:checkedEiSecteur__Forme}>
                                        <label for="ei-secteur-forme">Forme</label>

                                        <input name="{m:nameEiSecteur}" type="radio"
                                               value="{m:valueEiSecteur__Sante}"
                                               id="ei-secteur-sante"
                                               class="ei-secteur-not-input"
                                               {m:checkedEiSecteur__Sante}>
                                        <label for="ei-secteur-sante">Santé</label>

                                        <input name="{m:nameEiSecteur}" type="radio"
                                               value="{m:valueEiSecteur__Autre}"
                                               id="ei-secteur-autre"
                                               {m:checkedEiSecteur__Autre}>
                                        <label for="ei-secteur-autre" class="w100">
                                            <input type="text" name="{m:nameEiSecteurInput}"
                                                   placeholder="Autre"
                                                   value="{m:valueEiSecteurInput}" class="ei-secteur-autre-input">
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input name="{m:nameEiFonction}" type="text"
                                   value="{m:valueEiFonction}"
                                   placeholder="Fonction"
                            >
                        </td>
                    </tr>
                </table>

            <?php endif; ?>
            <div class="submit-container">
                <button type="submit" class="lee-red-button">JE VALIDE</button>
            </div>
        </form>
    </section>
</div>


<script>
    document.addEventListener("DOMContentLoaded", function (event) {
        $(document).ready(function () {
            var jWidget = $('#widget-account-info');
            var phoneCountries = <?php echo json_encode($phoneCountries); ?>;
            var oPhoneCountry = new PhoneCountry(jWidget, phoneCountries);

            function cleanAutreChoice() {
                jWidget.find('.ei-secteur-autre-input').val("");
            }


            jWidget.on('click', function (e) {
                var jTarget = $(e.target);
                if (jTarget.hasClass("phone-selector-trigger")) {
                    oPhoneCountry.select();
                    return false;
                }
                else if (jTarget.hasClass("ei-secteur-autre-input")) {
                    $("#ei-secteur-autre").prop('checked', true);
                    return false;
                }
                else if (jTarget.hasClass("ei-secteur-not-input")) {
                    cleanAutreChoice();
                }
            });


            jWidget.find('.ei-secteur-not-input').on('focus', function () {
                cleanAutreChoice();
            });

            window.onTheFlyForm.formInit(jWidget);

        });
    });
</script>



