<?php

use Kamille\Utils\ThemeHelper\KamilleThemeHelper;
use OnTheFlyForm\Helper\OnTheFlyFormHelper;
use Theme\LeeTheme;

KamilleThemeHelper::css("tool-form.css");
KamilleThemeHelper::css("table-form.css");
KamilleThemeHelper::css("create-account.css");
KamilleThemeHelper::js("phone-country-widget.js");

LeeTheme::useLib("onTheFlyForm");


$m = $v['formModel'];

$phoneCountries = $m['phoneCountries'];


?>
<div
        id="widget-create-account"
        class="widget widget-create-account tool-form {m:valueAccountType} step-1"
        style="padding-top: 50px; padding-bottom: 50px;">


    <form class="table-form no-label" action="{m:formAction}" method="{m:formMethod}"
          style="width: 560px">

        <?php OnTheFlyFormHelper::generateKey($m); ?>


        <?php if (true === $m['isPosted']): ?>
            <?php if ('' !== $m['successMessage']): ?>
                <p class="off-success-message">{m:successMessage}</p>
            <?php elseif ('' !== $m['errorMessage']): ?>
                <p class="off-error-message">{m:errorMessage}</p>
            <?php endif; ?>
        <?php endif; ?>


        <div class="title-with-radio">
            <div class="title-text">TYPE DE COMPTE</div>
            <div class="title-radio">
                <input name="{m:nameAccountType}" type="radio"
                       value="{m:valueAccountType__B2c}"
                       id="account-type-b2c"
                       {m:checkedAccountType__B2c}>
                <label for="account-type-b2c">Particulier</label>
                <input name="{m:nameAccountType}" type="radio"
                       value="{m:valueAccountType__B2b}"
                       id="account-type-b2b"
                       {m:checkedAccountType__B2b}>
                <label for="account-type-b2b">Professionnel</label>
            </div>
        </div>

        <div class="bloc-perso b2c-show">
            <div class="title-bar">MES INFORMATIONS DE COMPTE</div>
            <table>

                <tr>
                    <td>
                        <div class="control">
                            <span class="asterisk">*</span>
                            <input name="{m:nameEmailB2c}" type="text"
                                   value="{m:valueEmailB2c}"
                                   placeholder="E-mail"
                            >
                        </div>
                    </td>
                </tr>
                <?php if ('' !== $m['errorEmailB2c']): ?>
                    <tr data-error="{m:nameEmailB2c}">
                        <td>
                            <p class="error">{m:errorEmailB2c}</p>
                        </td>
                    </tr>
                <?php endif; ?>

                <tr>
                    <td>
                        <div class="control">
                            <span class="asterisk">*</span>
                            <input name="{m:namePassB2c}" type="password"
                                   value="{m:valuePassB2c}"
                                   placeholder="Mot de passe (6 caractères minimum)"
                            >
                        </div>
                    </td>
                </tr>
                <?php if ('' !== $m['errorPassB2c']): ?>
                    <tr data-error="{m:namePassB2c}">
                        <td>
                            <p class="error">{m:errorPassB2c}</p>
                        </td>
                    </tr>
                <?php endif; ?>
                <tr>
                    <td>
                        <div class="control">
                            <span class="asterisk">*</span>
                            <input name="{m:namePassConfirmB2c}" type="password"
                                   value="{m:valuePassConfirmB2c}"
                                   placeholder="Mot de passe à nouveau"
                            >
                        </div>
                    </td>
                </tr>
                <?php if ('' !== $m['errorPassConfirmB2c']): ?>
                    <tr data-error="{m:namePassConfirmB2c}">
                        <td>
                            <p class="error">{m:errorPassConfirmB2c}</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </table>

            <div class="title-bar">MES INFORMATIONS PERSONNELLES</div>
            <table>
                <tr>
                    <td>
                        <div class="radio-inputs">
                            <input name="{m:nameGenderB2c}" type="radio"
                                   value="{m:valueGenderB2c__Female}"
                                   id="gender-female"
                                   {m:checkedGenderB2c__Female}>
                            <label for="gender-female">Madame</label>
                            <input name="{m:nameGenderB2c}" type="radio"
                                   value="{m:valueGenderB2c__Male}"
                                   id="gender-male"
                                   {m:checkedGenderB2c__Male}>
                            <label for="gender-male">Monsieur</label>
                        </div>
                    </td>
                </tr>
                <?php if ('' !== $m['errorGenderB2c']): ?>
                    <tr data-error="{m:nameGenderB2c}">
                        <td>
                            <p class="error">{m:errorGenderB2c}</p>
                        </td>
                    </tr>
                <?php endif; ?>
                <tr>
                    <td>
                        <div class="control">
                            <span class="asterisk">*</span>
                            <input name="{m:nameLastNameB2c}" type="text"
                                   value="{m:valueLastNameB2c}"
                                   placeholder="Nom"
                            >
                        </div>
                    </td>
                </tr>
                <?php if ('' !== $m['errorLastNameB2c']): ?>
                    <tr data-error="{m:nameLastNameB2c}">
                        <td>
                            <p class="error">{m:errorLastNameB2c}</p>
                        </td>
                    </tr>
                <?php endif; ?>
                <tr>
                    <td>
                        <div class="control">
                            <span class="asterisk">*</span>
                            <input name="{m:nameFirstNameB2c}" type="text"
                                   value="{m:valueFirstNameB2c}"
                                   placeholder="Prénom"
                            >
                        </div>
                    </td>
                </tr>
                <?php if ('' !== $m['errorFirstNameB2c']): ?>
                    <tr data-error="{m:nameFirstNameB2c}">
                        <td>
                            <p class="error">{m:errorFirstNameB2c}</p>
                        </td>
                    </tr>
                <?php endif; ?>
                <tr>
                    <td>
                        <label class="birthday">Date de naissance</label>
                        <select name="{m:nameBirthdayDayB2c}">
                            <?php OnTheFlyFormHelper::selectOptions($m['optionsBirthdayDayB2c'], $m["valueBirthdayDayB2c"]); ?>
                        </select>
                        <select name="{m:nameBirthdayMonthB2c}">
                            <?php OnTheFlyFormHelper::selectOptions($m['optionsBirthdayMonthB2c'], $m["valueBirthdayMonthB2c"]); ?>
                        </select>
                        <select name="{m:nameBirthdayYearB2c}">
                            <?php OnTheFlyFormHelper::selectOptions($m['optionsBirthdayYearB2c'], $m["valueBirthdayYearB2c"]); ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="phone-line-td" style="position: relative">

                        <input class="phone-prefix-input" type="hidden" name="{m:namePhonePrefixB2c}"
                               value="{m:valuePhonePrefixB2c}">

                        <div class="phone-line phone-selector-trigger">
                            <div class="phone-prefix-selector-trigger phone-selector-trigger">
                                <div class="phone-flag flag flag-france phone-selector-trigger"></div>
                                <div class="phone-prefix phone-selector-trigger">+33</div>
                            </div>
                            <input name="{m:namePhoneB2c}" type="text"
                                   value="{m:valuePhoneB2c}"
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
                <?php if ('' !== $m['errorPhoneB2c']): ?>
                    <tr data-error="{m:namePhoneB2c}">
                        <td>
                            <p class="error">{m:errorPhoneB2c}</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </table>


            <div class="title-bar">MES ABONNEMENTS</div>
            <div class="container subscriptions">
                <div class="control">
                    <input type='checkbox' class="my-checkbox" name='{m:nameAboLeaderMailB2c}' value='1'
                           id="subscribe-promo-1" {m:checkedAboLeaderMailB2c}/>
                    <label for="subscribe-promo-1">Je souhaite recevoir les offres de Leaderfit' par mail (promos,
                        actus, bons
                        plans...)
                    </label>
                </div>
                <div class="control">
                    <input type='checkbox' class="my-checkbox" name='{m:nameAboLeaderPartnersMailB2c}' value='1'
                           id="subscribe-promo-2" {m:checkedAboLeaderPartnersMailB2c}/>
                    <label for="subscribe-promo-2">Je souhaite recevoir les bons plans des partenaires Leaderfit‘
                        par mail</label>
                </div>
                <div class="control">
                    <input type='checkbox' class="my-checkbox" name='{m:nameAboLeaderSmsB2c}' value='1'
                           id="subscribe-promo-3" {m:checkedAboLeaderSmsB2c}/>
                    <label for="subscribe-promo-3">Je souhaite recevoir les bons plans Leaderfit‘ par SMS</label>
                </div>
                <div class="control">
                    <input type='checkbox' class="my-checkbox" name='{m:nameAboAcceptRulesB2c}' value='1'
                           id="subscribe-promo-5" {m:checkedAboAcceptRulesB2c}/>
                    <label for="subscribe-promo-5">J’ai lu et j’accepte les conditions générales
                        d’utilisation</label>
                </div>
                <?php if ('' !== $m['errorAboAcceptRulesB2c']): ?>
                    <div class="control" data-error="{m:nameAboAcceptRulesB2c}">
                        <p class="error">{m:errorAboAcceptRulesB2c}</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>


        <!-- B2B -->
        <div class="bloc-pro b2b-show">
            <div class="stepper">
                <div class="step active step-trigger" data-step="step-1">
                    <div class="step-number step-trigger">
                        1
                        <div class="step-label step-trigger">MES IDENTIFIANTS</div>
                    </div>
                </div>
                <div class="linker"></div>
                <div class="step step-trigger" data-step="step-2">
                    <div class="step-number step-trigger">
                        2
                        <div class="step-label step-trigger">MON PROFIL</div>
                    </div>
                </div>
                <div class="linker"></div>
                <div class="step step-trigger" data-step="step-3">
                    <div class="step-number step-trigger">
                        3
                        <div class="step-label step-trigger">MES ABONNEMENTS</div>
                    </div>
                </div>
            </div>


            <input type="hidden" name="{m:nameKey}" value="{m:valueKey}">


            <div class="step-1-show step-1-content">
                <div class="title-bar">MES INFORMATIONS DE COMPTE</div>
                <table>

                    <tr>
                        <td>
                            <div class="control">
                                <span class="asterisk">*</span>
                                <input name="{m:nameEmail}" type="text"
                                       data-input-id="email"
                                       data-error-popout="email"
                                       value="{m:valueEmail}"
                                       placeholder="E-mail"
                                >
                            </div>
                        </td>
                    </tr>
                    <tr data-error="email">
                        <td>
                            <p class="error" data-error-text="1">{m:errorEmail}</p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="control">
                                <span class="asterisk">*</span>
                                <input name="{m:namePass}" type="password"
                                       data-input-id="pass"
                                       data-error-popout="pass"
                                       value="{m:valuePass}"
                                       placeholder="Mot de passe (6 caractères minimum)"
                                >
                            </div>
                        </td>
                    </tr>
                    <tr data-error="pass">
                        <td>
                            <p class="error" data-error-text="1">{m:errorPass}</p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="control">
                                <span class="asterisk">*</span>
                                <input name="{m:namePassConfirm}" type="password"
                                       data-input-id="passConfirm"
                                       data-error-popout="passConfirm"
                                       value="{m:valuePassConfirm}"
                                       placeholder="Mot de passe à nouveau"
                                >
                            </div>
                        </td>
                    </tr>
                    <tr data-error="passConfirm">
                        <td>
                            <p class="error" data-error-text="1">{m:errorPassConfirm}</p>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="step-2-show step-2-content">
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
                                               {m:checkedEiSecteur__Forme}>
                                        <label for="ei-secteur-forme">Forme</label>

                                        <input name="{m:nameEiSecteur}" type="radio"
                                               value="{m:valueEiSecteur__Sante}"
                                               id="ei-secteur-sante"
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


                <div class="title-bar">MES INFORMATIONS PERSONNELLES</div>
                <table>
                    <tr>
                        <td>
                            <div class="radio-inputs">
                                <input name="{m:nameGender}" type="radio"
                                       value="{m:valueGender__Female}"
                                       data-input-id="gender"
                                       data-error-popout="gender"
                                       id="gender-female"
                                       {m:checkedGender__Female}>
                                <label for="gender-female">Madame</label>
                                <input name="{m:nameGender}" type="radio"
                                       value="{m:valueGender__Male}"
                                       data-input-id="gender"
                                       data-error-popout="gender"
                                       id="gender-male"
                                       {m:checkedGender__Male}>
                                <label for="gender-male">Monsieur</label>
                            </div>
                        </td>
                    </tr>
                    <tr data-error="gender">
                        <td>
                            <p class="error" data-error-text="1">{m:errorGender}</p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="control">
                                <span class="asterisk">*</span>
                                <input name="{m:nameLastName}" type="text"
                                       value="{m:valueLastName}"
                                       data-input-id="lastName"
                                       data-error-popout="lastName"
                                       placeholder="Nom"
                                >
                            </div>
                        </td>
                    </tr>
                    <tr data-error="lastName">
                        <td>
                            <p class="error" data-error-text="1">{m:errorLastName}</p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="control">
                                <span class="asterisk">*</span>
                                <input name="{m:nameFirstName}" type="text"
                                       value="{m:valueFirstName}"
                                       data-input-id="firstName"
                                       data-error-popout="firstName"
                                       placeholder="Prénom"
                                >
                            </div>
                        </td>
                    </tr>
                    <tr data-error="firstName">
                        <td>
                            <p class="error" data-error-text="1">{m:errorFirstName}</p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label class="birthday">Date de naissance</label>
                            <select name="{m:nameBirthdayDay}">
                                <?php OnTheFlyFormHelper::selectOptions($m['optionsBirthdayDay'], $m["valueBirthdayDay"]); ?>
                            </select>
                            <select name="{m:nameBirthdayMonth}">
                                <?php OnTheFlyFormHelper::selectOptions($m['optionsBirthdayMonth'], $m["valueBirthdayMonth"]); ?>
                            </select>
                            <select name="{m:nameBirthdayYear}">
                                <?php OnTheFlyFormHelper::selectOptions($m['optionsBirthdayYear'], $m["valueBirthdayYear"]); ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="phone-line-td is-pro" style="position: relative">

                            <input class="phone-prefix-input" type="hidden" name="{m:namePhonePrefix}"
                                   value="{m:valuePhonePrefix}">

                            <div class="phone-line phone-selector-trigger">
                                <div class="phone-prefix-selector-trigger phone-selector-trigger">
                                    <div class="phone-flag flag flag-france phone-selector-trigger"></div>
                                    <div class="phone-prefix phone-selector-trigger">+33</div>
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

            </div>


            <div class="step-3-show step-3-content">
                <div class="title-bar">MES ABONNEMENTS</div>
                <div class="container subscriptions">
                    <div class="control">
                        <input type='checkbox' class="my-checkbox" name='{m:nameAboLeaderMail}' value='1'
                               id="subscribe-promoo-1"
                               {m:checkedAboLeaderMail}
                        />
                        <label for="subscribe-promoo-1">Je souhaite recevoir les offres de Leaderfit' par mail (promos,
                            actus, bons
                            plans...)
                        </label>
                    </div>
                    <div class="control">
                        <input type='checkbox' class="my-checkbox" name='{m:nameAboLeaderPartnersMail}' value='1'
                               id="subscribe-promoo-2"
                               {m:checkedAboLeaderPartnersMail}
                        />
                        <label for="subscribe-promoo-2">Je souhaite recevoir les bons plans des partenaires Leaderfit‘
                            par mail</label>
                    </div>
                    <div class="control">
                        <input type='checkbox' class="my-checkbox" name='{m:nameAboLeaderSms}' value='1'
                               id="subscribe-promoo-3"
                               {m:checkedAboLeaderSms}
                        />
                        <label for="subscribe-promoo-3">Je souhaite recevoir les bons plans Leaderfit‘ par SMS</label>
                    </div>
                    <div class="control">
                        <input type='checkbox' class="my-checkbox" name='{m:nameAcceptRules}' value='1'
                               id="subscribe-promoo-5"
                               data-input-id="acceptRules"
                               data-error-popout="acceptRules"
                               {m:checkedAcceptRules}
                        />
                        <label for="subscribe-promoo-5">J’ai lu et j’accepte les conditions générales
                            d’utilisation</label>
                    </div>
                    <?php if ('' !== $m['errorAboAcceptRules']): ?>
                        <div class="control" data-error="{m:nameAboAcceptRules}">
                            <p class="error" data-error-text="1">{m:errorAcceptRules}</p>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="title-bar t-center">SUIVEZ NOUS</div>
                <div class="social-icons-container">
                    <div class="social-icon social-icon-facebook"></div>
                    <div class="social-icon social-icon-twitter"></div>
                    <div class="social-icon social-icon-instagram"></div>
                    <div class="social-icon social-icon-pinterest"></div>
                </div>
            </div>
        </div>


        <div class="submit-container">
            <button type="submit" class="lee-red-button b2c-show">JE VALIDE</button>
            <button type="submit" class="lee-red-button b2b-show step-1-hide go-prev-step">ÉTAPE PRÉCÉDENTE</button>
            <button type="submit" class="lee-red-button b2b-show step-3-hide go-next-step">ÉTAPE SUIVANTE</button>
            <button type="submit" class="lee-red-button b2b-show step-1-hide step-2-hide go-process-form">JE VALIDE
            </button>
        </div>
    </form>
</div>


<script>
    document.addEventListener("DOMContentLoaded", function (event) {

        var api = ekomApi.inst();

        $(document).ready(function () {


            var jWidget = $('#widget-create-account');
            var jStepper = jWidget.find('.stepper');
            var jPro = jWidget.find('.bloc-pro');
            var jPerso = jWidget.find('.bloc-perso');

            var jProStep1 = jPro.find('.step-1-content');
            var jProStep2 = jPro.find('.step-2-content');
            var jProStep3 = jPro.find('.step-3-content');
            var jForm = jWidget.find('form:first');
            var jError = jWidget.find('.form-handling-error');


            window.onTheFlyForm.formInit(jForm);


            var phoneCountries = <?php echo json_encode($phoneCountries); ?>;

            // b2c
            var oPhoneCountryB2c = new PhoneCountry(jPerso, phoneCountries);

            // b2b
            var oPhoneCountryB2b = new PhoneCountry(jProStep2, phoneCountries);


            function cleanErrors() {
                jError.hide();
                jError.empty();
            }

            function getCurrentStep() {
                if (jWidget.hasClass("step-3")) {
                    return 3;
                }
                else if (jWidget.hasClass("step-2")) {
                    return 2;
                }
                return 1;
            }

            function goPreviousStep() {
                var curStep = getCurrentStep();
                curStep--;
                if (curStep < 1) {
                    curStep = 1;
                }
                gotoStep("step-" + curStep);
            }

            function goNextStep() {
                var curStep = getCurrentStep();
                curStep++;
                if (curStep > 3) {
                    curStep = 3;
                }
                gotoStep("step-" + curStep);
            }

            function gotoStep(stepString) {
                if (false === jWidget.hasClass(stepString)) {
                    jWidget.removeClass("step-1 step-2 step-3");
                    jWidget.addClass(stepString);
                    jStepper.find(".step").each(function () {
                        if (stepString === $(this).attr('data-step')) {
                            $(this).addClass("active");
                        }
                        else {
                            $(this).removeClass("active");
                        }
                    });
                }
            }

            function checkErrors(fnSuccess) {

                var curStep = getCurrentStep();
                if (1 === curStep) {
                    var email = jProStep1.find('input[data-input-id="email"]').val();
                    var pass = jProStep1.find('input[data-input-id="pass"]').val();
                    var passConfirm = jProStep1.find('input[data-input-id="passConfirm"]').val();


                    api.utils.request('json', 'create_account--check_step_one', {
                        email: email,
                        pass: pass,
                        passConfirm: passConfirm
                    }, function (out) {

                        var isValid = out.isValid;
                        var model = out.model;

                        if (false === isValid) {
                            window.onTheFlyForm.injectValidationErrors(jForm, model);
                        }
                        else {
                            fnSuccess();
                        }
                    }, null, "ThisApp");
                }
                else if (2 === curStep) {
                    var company = jProStep2.find('input[data-input-id="company"]').val();
                    var siret = jProStep2.find('input[data-input-id="siret"]').val();
                    var tva = jProStep2.find('input[data-input-id="tva"]').val();
                    var lastName = jProStep2.find('input[data-input-id="lastName"]').val();
                    var firstName = jProStep2.find('input[data-input-id="firstName"]').val();


                    api.utils.request('json', 'create_account--check_step_two', {
                        company: company,
                        siret: siret,
                        tva: tva,
                        lastName: lastName,
                        firstName: firstName
                    }, function (out) {
                        var isValid = out.isValid;
                        var model = out.model;
                        if (false === isValid) {
                            window.onTheFlyForm.injectValidationErrors(jForm, model);
                        }
                        else {
                            fnSuccess();
                        }
                    }, null, "ThisApp");
                }
            }


            function processForm() {

                var curStep = getCurrentStep();
                cleanErrors();

                if (3 === curStep) {

                    var siret = jProStep2.find('input[data-input-id="siret"]').val();
                    var tva = jProStep2.find('input[data-input-id="tva"]').val();
                    var lastName = jProStep2.find('input[data-input-id="lastName"]').val();
                    var firstName = jProStep2.find('input[data-input-id="firstName"]').val();

                    var serialize = jForm.serialize();


                    api.utils.request('json', 'create_account--check_step_three', serialize, function (out) {
                        if (true === out.isValid) {
                            window.location.href = out.uriRedirect;
                        }
                        else {
                            var model = out.model;
                            if ("formHandlingError" in model) {
                                var error = model.formHandlingError;
                                jError.show();
                                jError.append(error);
                            }
                        }
                    }, null, "ThisApp");
                }
            }


            jWidget.find('.title-radio input').on("change", function () {
                var value = $(this).val();
                console.log(value);

                jWidget.removeClass('b2b b2c');
                jWidget.addClass(value);
            });


            jWidget.on('click', function (e) {
                var jTarget = $(e.target);
                if (jTarget.hasClass("step-trigger")) {
                    var clickedStep = jTarget.closest(".step").attr("data-step");
                    var clickedStepNumber = parseInt(clickedStep.substr(5));
                    var curStep = getCurrentStep();


                    if (clickedStepNumber > curStep) {
                        checkErrors(function () {
                            gotoStep(clickedStep);
                        });
                    }
                    else {
                        gotoStep(clickedStep);
                    }
                    return false;
                }
                else if (
                    jTarget.hasClass("go-prev-step") ||
                    jTarget.hasClass("go-next-step")
                ) {
                    if (jTarget.hasClass("go-prev-step")) {
                        goPreviousStep();
                    }
                    else {
                        checkErrors(function () {
                            goNextStep();
                        });
                    }
                    return false;
                }
                else if (jTarget.hasClass("go-process-form")) {
                    processForm();
                    return false;
                }
                else if (jTarget.hasClass("phone-selector-trigger")) {
                    var jTd = jTarget.closest("td");
                    if (false === jTd.hasClass("is-pro")) {
                        oPhoneCountryB2c.select();
                    }
                    else {
                        oPhoneCountryB2b.select();
                    }
                    return false;
                }
                else if (jTarget.hasClass("ei-secteur-autre-input")) {
                    $("#ei-secteur-autre").prop('checked', true);
                    return false;
                }
            });
        });
    });
</script>