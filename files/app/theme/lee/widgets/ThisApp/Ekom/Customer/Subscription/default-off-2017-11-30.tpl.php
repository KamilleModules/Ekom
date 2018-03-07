<?php

use FormTools\Rendering\FormToolsRenderer;
use FormTools\Validation\OnTheFlyFormValidator;
use Kamille\Utils\ThemeHelper\KamilleThemeHelper;
use OnTheFlyForm\Helper\OnTheFlyFormHelper;
use Theme\LeeTheme;

KamilleThemeHelper::css("customer-all.css");
KamilleThemeHelper::css("tool-form.css");
KamilleThemeHelper::css("table-form.css");
LeeTheme::useLib('roundCheckbox');


$m = $v['formModel'];

?>

<div class="widget widget-customer-subscription tool-form">

    <?php if (true === $m['isPosted']): ?>
        <?php if ('' !== $m['successMessage']): ?>
            <p class="off-success-message form-handling-success">{m:successMessage}</p>
        <?php elseif ('' !== $m['errorMessage']): ?>
            <p class="off-error-message form-handling-error">{m:errorMessage}</p>
        <?php endif; ?>
    <?php endif; ?>

    <!--    <div class="bar-red">MON COMPTE</div>-->
    <section>
        <form class="table-form no-label" action="{m:formAction}"
              method="{m:formMethod}"

        >
            <div class="bar-gray">MES ABONNEMENTS</div>

            <?php OnTheFlyFormHelper::generateKey($m); ?>

            <div class="container subscriptions">
                <div class="control">
                    <div class="round-checkbox" data-id="subscribe-promo-1"></div>
                    <input type='checkbox' class="round-checkbox" name='{m:nameAboLeaderMail}' value='1'
                           id="subscribe-promo-1" {m:checkedAboLeaderMail}/>
                    <label for="subscribe-promo-1">Je souhaite recevoir les offres de Leaderfit' par mail
                        (promos,
                        actus, bons
                        plans...)
                    </label>
                </div>
                <div class="control">
                    <div class="round-checkbox" data-id="subscribe-promo-2"></div>
                    <input type='checkbox' class="round-checkbox" name='{m:nameAboLeaderPartnersMail}' value='1'
                           id="subscribe-promo-2" {m:checkedAboLeaderPartnersMail}/>
                    <label for="subscribe-promo-2">Je souhaite recevoir les bons plans des partenaires Leaderfit‘
                        par mail</label>
                </div>
                <div class="control">
                    <div class="round-checkbox" data-id="subscribe-promo-3"></div>
                    <input type='checkbox' class="round-checkbox" name='{m:nameAboLeaderSms}' value='1'
                           id="subscribe-promo-3" {m:checkedAboLeaderSms}/>
                    <label for="subscribe-promo-3">Je souhaite recevoir les bons plans Leaderfit‘ par SMS</label>
                </div>
            </div>


            <div class="social-container">
                <div class="bar-gray centered">SUIVEZ-NOUS</div>
                <div class="social-icons-bar">
                    <a href="#" class="icon icon-facebook">&nbsp;</a>
                    <a href="#" class="icon icon-twitter">&nbsp;</a>
                    <a href="#" class="icon icon-instagram">&nbsp;</a>
                    <a href="#" class="icon icon-pinterest">&nbsp;</a>
                </div>
            </div>


            <div class="submit-container">
                <button type="submit" class="lee-red-button">JE VALIDE</button>
            </div>
        </form>
    </section>
</div>



