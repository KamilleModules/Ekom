<?php

use Kamille\Utils\ThemeHelper\KamilleThemeHelper;
use Theme\LeeTheme;

KamilleThemeHelper::css("tool-form.css");
KamilleThemeHelper::css("table-form.css");
KamilleThemeHelper::css("create-account.css");

LeeTheme::useLib("onTheFlyForm");


$phoneCountries = $v['phoneCountries'];


?>
<div
        id="widget-create-account"
        class="widget widget-create-account tool-form {valueAccountType} step-1"
        style="padding-top: 50px; padding-bottom: 50px;">


    <form class="table-form no-label" action="<?php echo $v['formAction']; ?>" method="<?php echo $v['formMethod']; ?>"
          style="width: 420px">

        <input type="hidden" name="{nameKey}" value="{valueKey}">


        <div class="form-handling-error hidden">{formHandlingError}</div>


        <?php if (($v['errorForm'])): ?>
            <div class="error"><?php echo $v['errorForm']; ?></div>
        <?php endif; ?>


        <div class="title-with-radio">
            <div class="title-text">TYPE DE COMPTE</div>
            <div class="title-radio">
                <input name="{nameAccountType}" type="radio"
                       value="{valueAccountType__B2C}"
                       id="account-type-b2c"
                       {checkedAccountType__B2C}>
                <label for="account-type-b2c">Particulier</label>
                <input name="{nameAccountType}" type="radio"
                       value="{valueAccountType__B2B}"
                       id="account-type-b2b"
                       {checkedAccountType__B2B}>
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
                            <input name="{nameEmailB2c}" type="text"
                                   value="{valueEmailB2c}"
                                   placeholder="E-mail"
                            >
                        </div>
                    </td>
                </tr>
                <?php if ('' !== $v['errorEmailB2c']): ?>
                    <tr>
                        <td>
                            <p class="error">{errorEmailB2c}</p>
                        </td>
                    </tr>
                <?php endif; ?>

                <tr>
                    <td>
                        <div class="control">
                            <span class="asterisk">*</span>
                            <input name="{namePassB2c}" type="password"
                                   value="{valuePassB2c}"
                                   placeholder="Mot de passe (6 caractères minimum)"
                            >
                        </div>
                    </td>
                </tr>
                <?php if ('' !== $v['errorPassB2c']): ?>
                    <tr>
                        <td>
                            <p class="error">{errorPassB2c}</p>
                        </td>
                    </tr>
                <?php endif; ?>
                <tr>
                    <td>
                        <div class="control">
                            <span class="asterisk">*</span>
                            <input name="{namePassConfirmB2c}" type="password"
                                   value="{valuePassConfirmB2c}"
                                   placeholder="Mot de passe à nouveau"
                            >
                        </div>
                    </td>
                </tr>
                <?php if ('' !== $v['errorPassConfirmB2c']): ?>
                    <tr>
                        <td>
                            <p class="error">{errorPassConfirmB2c}</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </table>

            <div class="title-bar">MES INFORMATIONS PERSONNELLES</div>
            <table>
                <tr>
                    <td>
                        <div class="radio-inputs">
                            <input name="{nameGenderB2c}" type="radio"
                                   value="{valueGenderB2c__Female}"
                                   id="gender-female"
                                   {checkedGenderB2c__Female}>
                            <label for="gender-female">Madame</label>
                            <input name="{nameGenderB2c}" type="radio"
                                   value="{valueGenderB2c__Male}"
                                   id="gender-male"
                                   {checkedGenderB2c__Male}>
                            <label for="gender-male">Monsieur</label>
                        </div>
                    </td>
                </tr>
                <?php if ('' !== $v['errorGenderB2c']): ?>
                    <tr>
                        <td>
                            <p class="error">{errorGenderB2c}</p>
                        </td>
                    </tr>
                <?php endif; ?>
                <tr>
                    <td>
                        <div class="control">
                            <span class="asterisk">*</span>
                            <input name="{nameLastNameB2c}" type="text"
                                   value="{valueLastNameB2c}"
                                   placeholder="Nom"
                            >
                        </div>
                    </td>
                </tr>
                <?php if ('' !== $v['errorLastNameB2c']): ?>
                    <tr>
                        <td>
                            <p class="error">{errorLastNameB2c}</p>
                        </td>
                    </tr>
                <?php endif; ?>
                <tr>
                    <td>
                        <div class="control">
                            <span class="asterisk">*</span>
                            <input name="{nameFirstNameB2c}" type="text"
                                   value="{valueFirstNameB2c}"
                                   placeholder="Prénom"
                            >
                        </div>
                    </td>
                </tr>
                <?php if ('' !== $v['errorFirstNameB2c']): ?>
                    <tr>
                        <td>
                            <p class="error">{errorFirstNameB2c}</p>
                        </td>
                    </tr>
                <?php endif; ?>
                <tr>
                    <td>
                        <input name="{nameBirthdayB2c}" type="text"
                               value="{valueBirthdayB2c}"
                               placeholder="Date de naissance"
                        >
                    </td>
                </tr>
                <tr>
                    <td class="phone-line-td" style="position: relative">

                        <input class="phone-prefix-input" type="hidden" name="{namePhonePrefixB2c}"
                               value="{valuePhonePrefixB2c}">

                        <div class="phone-line phone-selector-trigger">
                            <div class="phone-prefix-selector-trigger phone-selector-trigger">
                                <div class="phone-flag flag flag-france phone-selector-trigger"></div>
                                <div class="phone-prefix phone-selector-trigger">+33</div>
                            </div>
                            <input name="{namePhoneB2c}" type="text"
                                   value="{valuePhoneB2c}"
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


            <div class="title-bar">MES ABONNEMENTS</div>
            <div class="container subscriptions">
                <div class="control">
                    <input type='checkbox' class="my-checkbox" name='{nameAboLeaderMailB2c}' value='1'
                           id="subscribe-promo-1" {checkedAboLeaderMailB2c}/>
                    <label for="subscribe-promo-1">Je souhaite recevoir les offres de Leaderfit' par mail (promos,
                        actus, bons
                        plans...)
                    </label>
                </div>
                <div class="control">
                    <input type='checkbox' class="my-checkbox" name='{nameAboLeaderPartnersMailB2c}' value='1'
                           id="subscribe-promo-2" {checkedAboLeaderPartnersMailB2c}/>
                    <label for="subscribe-promo-2">Je souhaite recevoir les bons plans des partenaires Leaderfit‘
                        par mail</label>
                </div>
                <div class="control">
                    <input type='checkbox' class="my-checkbox" name='{nameAboLeaderSmsB2c}' value='1'
                           id="subscribe-promo-3" {checkedAboLeaderSmsB2c}/>
                    <label for="subscribe-promo-3">Je souhaite recevoir les bons plans Leaderfit‘ par SMS</label>
                </div>
                <div class="control">
                    <input type='checkbox' class="my-checkbox" name='{nameAboFideliteB2c}' value='1'
                           id="subscribe-promo-4" {checkedAboFideliteB2c}/>
                    <label for="subscribe-promo-4">Je profite gratuitement des avantages du programme de
                        fidélité
                        <br>
                        <span for="subscribe-promo-4" class="red-label">Je découvre tous les avantages du programme de fidélité</span>
                    </label>

                </div>
                <div class="control mt40">
                    <input type='checkbox' class="my-checkbox" name='{nameAcceptRulesB2c}' value='1'
                           id="subscribe-promo-5" {checkedAcceptRulesB2c}/>
                    <label for="subscribe-promo-5">J’ai lu et j’accepte les conditions générales
                        d’utilisation</label>
                </div>
                <?php if ('' !== $v['errorAcceptRulesB2c']): ?>
                    <div class="control">
                        <p class="error">{errorAcceptRulesB2c}</p>
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


            <input type="hidden" name="{nameKey}" value="{valueKey}">


            <div class="step-1-show step-1-content">
                <div class="title-bar">MES INFORMATIONS DE COMPTE</div>
                <table>

                    <tr>
                        <td>
                            <div class="control">
                                <span class="asterisk">*</span>
                                <input name="{nameEmail}" type="text"
                                       data-input-id="email"
                                       data-error-popout="email"
                                       value="{valueEmail}"
                                       placeholder="E-mail"
                                >
                            </div>
                        </td>
                    </tr>
                    <tr data-error="email">
                        <td>
                            <p class="error" data-error-text="1">{errorEmail}</p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="control">
                                <span class="asterisk">*</span>
                                <input name="{namePass}" type="password"
                                       data-input-id="pass"
                                       data-error-popout="pass"
                                       value="{valuePass}"
                                       placeholder="Mot de passe (6 caractères minimum)"
                                >
                            </div>
                        </td>
                    </tr>
                    <tr data-error="pass">
                        <td>
                            <p class="error" data-error-text="1">{errorPass}</p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="control">
                                <span class="asterisk">*</span>
                                <input name="{namePassConfirm}" type="password"
                                       data-input-id="passConfirm"
                                       data-error-popout="passConfirm"
                                       value="{valuePassConfirm}"
                                       placeholder="Mot de passe à nouveau"
                                >
                            </div>
                        </td>
                    </tr>
                    <tr data-error="passConfirm">
                        <td>
                            <p class="error" data-error-text="1">{errorPassConfirm}</p>
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
                                <input name="{nameCompany}" type="text"
                                       value="{valueCompany}"
                                       data-input-id="company"
                                       data-error-popout="company"
                                       placeholder="Raison sociale"
                                >
                            </div>
                        </td>
                    </tr>
                    <tr data-error="company">
                        <td>
                            <p class="error" data-error-text="1">{errorCompany}</p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="control">
                                <span class="asterisk">*</span>
                                <input name="{nameSiret}" type="text"
                                       value="{valueSiret}"
                                       data-input-id="siret"
                                       data-error-popout="siret"
                                       placeholder="N° SIRET"
                                >
                            </div>
                        </td>
                    </tr>
                    <tr data-error="siret">
                        <td>
                            <p class="error" data-error-text="1">{errorSiret}</p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="control">
                                <span class="asterisk">*</span>
                                <input name="{nameTva}" type="text"
                                       value="{valueTva}"
                                       data-input-id="tva"
                                       data-error-popout="tva"
                                       placeholder="TVA intracommunautaire"
                                >
                            </div>
                        </td>
                    </tr>
                    <tr data-error="tva">
                        <td>
                            <p class="error" data-error-text="1">{errorTva}</p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="pro-extra-info">
                                <div class="bloc">
                                    <div class="title-label">Type de société</div>
                                    <div class="radio-inputs">
                                        <input name="{nameEiType}" type="radio"
                                               value="{valueEiType__Entreprise}"
                                               id="ei-type-entreprise"
                                               {checkedEiType__Entreprise}>
                                        <label for="ei-type-entreprise">Entreprise</label>
                                        <input name="{nameEiType}" type="radio"
                                               value="{valueEiType__Association}"
                                               id="ei-type-association"
                                               {checkedEiType__Association}>
                                        <label for="ei-type-association">Association</label>

                                        <input name="{nameEiType}" type="radio"
                                               value="{valueEiType__Independant}"
                                               id="ei-type-independant"
                                               {checkedEiType__Independant}>
                                        <label for="ei-type-independant">Independant</label>
                                    </div>
                                </div>
                                <div class="bloc">
                                    <div class="title-label">Secteur d'activité</div>
                                    <div class="radio-inputs">
                                        <input name="{nameEiSecteur}" type="radio"
                                               value="{valueEiSecteur__Forme}"
                                               id="ei-secteur-forme"
                                               {checkedEiSecteur__Forme}>
                                        <label for="ei-secteur-forme">Forme</label>

                                        <input name="{nameEiSecteur}" type="radio"
                                               value="{valueEiSecteur__Sante}"
                                               id="ei-secteur-sante"
                                               {checkedEiSecteur__Sante}>
                                        <label for="ei-secteur-sante">Santé</label>

                                        <input name="{nameEiSecteur}" type="radio"
                                               value="{valueEiSecteur__Autre}"
                                               id="ei-secteur-autre"
                                               {checkedEiSecteur__Autre}>
                                        <label for="ei-secteur-autre" class="w100">
                                            <input type="text" name="{nameEiSecteurInput}"
                                                   value="{valueEiSecteurInput}">
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input name="{nameFonction}" type="text"
                                   value="{valueFonction}"
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
                                <input name="{nameGender}" type="radio"
                                       value="{valueGender__Female}"
                                       data-input-id="gender"
                                       data-error-popout="gender"
                                       id="gender-female"
                                       {checkedGender__Female}>
                                <label for="gender-female">Madame</label>
                                <input name="{nameGender}" type="radio"
                                       value="{valueGender__Male}"
                                       data-input-id="gender"
                                       data-error-popout="gender"
                                       id="gender-male"
                                       {checkedGender__Male}>
                                <label for="gender-male">Monsieur</label>
                            </div>
                        </td>
                    </tr>
                    <tr data-error="gender">
                        <td>
                            <p class="error" data-error-text="1">{errorGender}</p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="control">
                                <span class="asterisk">*</span>
                                <input name="{nameLastName}" type="text"
                                       value="{valueLastName}"
                                       data-input-id="lastName"
                                       data-error-popout="lastName"
                                       placeholder="Nom"
                                >
                            </div>
                        </td>
                    </tr>
                    <tr data-error="lastName">
                        <td>
                            <p class="error" data-error-text="1">{errorLastName}</p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="control">
                                <span class="asterisk">*</span>
                                <input name="{nameFirstName}" type="text"
                                       value="{valueFirstName}"
                                       data-input-id="firstName"
                                       data-error-popout="firstName"
                                       placeholder="Prénom"
                                >
                            </div>
                        </td>
                    </tr>
                    <tr data-error="firstName">
                        <td>
                            <p class="error" data-error-text="1">{errorFirstName}</p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input name="{nameBirthday}" type="text"
                                   value="{valueBirthday}"
                                   placeholder="Date de naissance"
                            >
                        </td>
                    </tr>
                    <tr>
                        <td class="phone-line-td is-pro" style="position: relative">

                            <input class="phone-prefix-input" type="hidden" name="{namePhonePrefix}"
                                   value="{valuePhonePrefix}">

                            <div class="phone-line phone-selector-trigger">
                                <div class="phone-prefix-selector-trigger phone-selector-trigger">
                                    <div class="phone-flag flag flag-france phone-selector-trigger"></div>
                                    <div class="phone-prefix phone-selector-trigger">+33</div>
                                </div>
                                <input name="{namePhone}" type="text"
                                       value="{valuePhone}"
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
                        <input type='checkbox' class="my-checkbox" name='{nameAboLeaderMail}' value='1'
                               id="subscribe-promoo-1"
                               {checkedAboLeaderMail}
                        />
                        <label for="subscribe-promoo-1">Je souhaite recevoir les offres de Leaderfit' par mail (promos,
                            actus, bons
                            plans...)
                        </label>
                    </div>
                    <div class="control">
                        <input type='checkbox' class="my-checkbox" name='{nameAboLeaderPartnersMail}' value='1'
                               id="subscribe-promoo-2"
                               {checkedAboLeaderPartnersMail}
                        />
                        <label for="subscribe-promoo-2">Je souhaite recevoir les bons plans des partenaires Leaderfit‘
                            par mail</label>
                    </div>
                    <div class="control">
                        <input type='checkbox' class="my-checkbox" name='{nameAboLeaderSms}' value='1'
                               id="subscribe-promoo-3"
                               {checkedAboLeaderSms}
                        />
                        <label for="subscribe-promoo-3">Je souhaite recevoir les bons plans Leaderfit‘ par SMS</label>
                    </div>
                    <div class="control">
                        <input type='checkbox' class="my-checkbox" name='{nameAboFidelite}' value='1'
                               id="subscribe-promoo-4"
                               {checkedAboFidelite}
                        />
                        <label for="subscribe-promoo-4">Je profite gratuitement des avantages du programme de
                            fidélité
                            <br>
                            <span for="subscribe-promoo-4" class="red-label">Je découvre tous les avantages du programme de fidélité</span>
                        </label>

                    </div>
                    <div class="control mt40">
                        <input type='checkbox' class="my-checkbox" name='{nameAcceptRules}' value='1'
                               id="subscribe-promoo-5"
                               data-input-id="acceptRules"
                               data-error-popout="acceptRules"
                               {checkedAcceptRules}
                        />
                        <label for="subscribe-promoo-5">J’ai lu et j’accepte les conditions générales
                            d’utilisation</label>
                    </div>
                    <div class="control" data-error="acceptRulesB2c">
                        <p class="error" data-error-text="1">{errorAcceptRulesB2c}</p>
                    </div>
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
            var isPhonePro = false;

            // b2c
            var jB2cPhoneSelector = jPerso.find(".phone-line-td .phone-prefix-selector");
            var jB2cPhonePrefixInput = jPerso.find(".phone-prefix-input");
            var jB2cPhoneFlag = jPerso.find(".phone-flag");
            var jB2cPrefix = jPerso.find(".phone-prefix");

            // b2b
            var jPhoneSelector = jProStep2.find(".phone-line-td .phone-prefix-selector");
            var jPhonePrefixInput = jProStep2.find(".phone-prefix-input");
            var jPhoneFlag = jProStep2.find(".phone-flag");
            var jPrefix = jProStep2.find(".phone-prefix");

            var phoneCountries = <?php echo json_encode($phoneCountries); ?>;
            var classPhoneToRemove = [];
            for (var i in phoneCountries) {
                var info = phoneCountries[i];
                var country = info.country;
                classPhoneToRemove.push('flag-' + country);
            }

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


            function listenToPhoneSelect() {
                $(document).on('click.createAccount', function (e) {
                    var jTarget = $(e.target);
                    $(document).off('click.createAccount');


                    if (jTarget.hasClass("phone-choice")) {
                        var jLi = jTarget.closest("li");
                        var prefix = jLi.attr("data-prefix");
                        var country = jLi.attr("data-country");
                        jB2cPhonePrefixInput.attr("value", prefix);
                        var label = jLi.find(".phone").text();

                        if (false === isPhonePro) {
                            jB2cPhoneFlag.removeClass(classPhoneToRemove.join(" "));
                            jB2cPhoneFlag.addClass('flag-' + country);
                            jB2cPrefix.text(label);
                            jB2cPhoneSelector.removeClass("selecting");
                        }
                        else {
                            jPhoneSelector.removeClass("selecting");
                            jPhoneFlag.removeClass(classPhoneToRemove.join(" "));
                            jPhoneFlag.addClass('flag-' + country);
                            jPrefix.text(label);
                        }
                    }
                });
            }


            jWidget.find('.title-radio input').on("change", function () {
                var value = $(this).val();

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
                        isPhonePro = false;
                        jB2cPhoneSelector.addClass("selecting");
                    }
                    else {
                        isPhonePro = true;
                        jPhoneSelector.addClass("selecting");
                    }
                    listenToPhoneSelect();
                    return false;
                }
            });
        });
    });
</script>