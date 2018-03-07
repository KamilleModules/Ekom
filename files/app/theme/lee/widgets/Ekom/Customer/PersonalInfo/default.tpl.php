<?php

use Kamille\Utils\ThemeHelper\KamilleThemeHelper;
use OnTheFlyForm\Helper\OnTheFlyFormHelper;

KamilleThemeHelper::css("customer-all.css");

?>

<div class="widget widget-personal-info">

    <div class="title">TYPE DE COMPTE: {accountType}</div>

    <?php if ('b2c' === $v['accountType']): ?>
        <div class="bar-red">MES INFORMATIONS PERSONNELLES</div>
        <section>
            <form class="table-form no-label" action="<?php echo $v['formAction']; ?>"
                  method="<?php echo $v['formMethod']; ?>"
                  style="width: 420px">

                <?php OnTheFlyFormHelper::generateKey($v); ?>


                <div class="form-handling-error hidden">{formHandlingError}</div>
                <?php if (($v['errorForm'])): ?>
                    <div class="error"><?php echo $v['errorForm']; ?></div>
                <?php endif; ?>

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
                        <td>
                            <input name="{namePhoneB2c}" type="text"
                                   value="{valuePhoneB2c}"
                                   placeholder="Téléphone"
                            >
                        </td>
                    </tr>
                </table>

                <div class="submit-container">
                    <button type="submit" class="lee-red-button">JE VALIDE</button>
                </div>
            </form>
        </section>

    <?php else: ?>
    <?php endif; ?>
</div>



