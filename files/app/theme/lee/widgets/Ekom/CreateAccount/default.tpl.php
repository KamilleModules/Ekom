<div class="main container">
    <div class="inner-container">
        <div class="preface"></div>
        <div id="page-columns" class="columns">
            <div class="column-main">
                <div class="account-create">
                    <div class="page-title">
                        <h1>Create an Account</h1>
                    </div>
                    <form action="{formAction}" method="{formMethod}" id="form-validate">
                        <div class="fieldset">
                            <input name="{nameKey}" value="{valueKey}" type="hidden">
                            <h2 class="legend">Personal Information</h2>
                            <ul class="form-list">
                                <!--                                <li class="fields">-->
                                <!--                                    <div class="customer-name-middlename">-->
                                <!--                                        <div class="field name-firstname">-->
                                <!--                                            <label for="firstname" class="required"><em>*</em>First Name</label>-->
                                <!--                                            <div class="input-box">-->
                                <!--                                                <input id="firstname" name="firstname" value="" title="First Name"-->
                                <!--                                                       maxlength="255" class="input-text required-entry" type="text">-->
                                <!--                                            </div>-->
                                <!--                                            <div class="field name-lastname">-->
                                <!--                                                <label for="lastname" class="required"><em>*</em>Last Name</label>-->
                                <!--                                                <div class="input-box">-->
                                <!--                                                    <input id="lastname" name="lastname" value="" title="Last Name"-->
                                <!--                                                           maxlength="255" class="input-text required-entry"-->
                                <!--                                                           type="text">-->
                                <!--                                                </div>-->
                                <!--                                            </div>-->
                                <!--                                        </div>-->
                                <!--                                </li>-->
                                <li>
                                    <label for="email_address" class="required"><em>*</em>Email Address</label>
                                    <div class="input-box">

                                        <?php $classError = ('' !== $v['errorEmail']) ? 'validation-failed' : ''; ?>

                                        <input name="{nameEmail}" id="email_address"
                                               value="<?php echo htmlspecialchars($v['valueEmail']); ?>"
                                               title="Email Address"
                                               class="input-text validate-email required-entry <?php echo $classError; ?>"
                                               type="text">

                                        <?php if ('' !== $v['errorEmail']): ?>
                                            <div class="validation-advice">
                                                {errorEmail}
                                            </div>
                                        <?php endif; ?>

                                    </div>
                                </li>
                                <li class="control">
                                    <div class="input-box">
                                        <input name="{nameNewsletter}" title="Sign Up for Newsletter" value="1"
                                               id="is_subscribed" class="checkbox" type="checkbox"
                                               {checkedNewsletter}
                                        >
                                    </div>
                                    <label for="is_subscribed">Sign Up for Newsletter</label>
                                </li>
                            </ul>
                        </div>
                        <div class="fieldset">
                            <h2 class="legend">Login Information</h2>
                            <ul class="form-list">
                                <li class="fields">
                                    <div class="field">
                                        <label for="password" class="required"><em>*</em>Password</label>
                                        <div class="input-box">
                                            <?php $classError = ('' !== $v['errorPass']) ? 'validation-failed' : ''; ?>
                                            <input name="{namePass}" id="password" title="Password"
                                                   class="input-text required-entry validate-password <?php echo $classError; ?>"
                                                   type="password"
                                                   value="<?php echo htmlspecialchars($v['valuePass']); ?>"
                                            >
                                            <?php if ('' !== $v['errorPass']): ?>
                                                <div class="validation-advice">
                                                    {errorPass}
                                                </div>
                                            <?php endif; ?>

                                        </div>
                                    </div>
                                    <div class="field">
                                        <label for="confirmation" class="required"><em>*</em>Confirm Password</label>
                                        <div class="input-box">
                                            <?php $classError = ('' !== $v['errorPass2']) ? 'validation-failed' : ''; ?>
                                            <input name="{namePass2}" title="Confirm Password" id="confirmation"
                                                   class="input-text required-entry validate-cpassword <?php echo $classError; ?>"
                                                   type="password"
                                                   value="<?php echo htmlspecialchars($v['valuePass2']); ?>"
                                            >
                                            <?php if ('' !== $v['errorPass2']): ?>
                                                <div class="validation-advice">
                                                    {errorPass2}
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </li>

                                <li id="captcha-input-box-user_create">
                                    <label for="captcha_user_create" class="required"><em>*</em>Please type the letters
                                        below</label>
                                    <div class="input-box captcha">
                                        <input name="captcha[user_create]" class="input-text required-entry"
                                               id="captcha_user_create" type="text">
                                    </div>
                                </li>
                                <li>
                                    <div class="captcha-image" id="captcha-image-box-user_create">
                                        <img id="captcha-reload" class="captcha-reload"
                                             src="http://ultimo.infortis-themes.com/demo/skin/frontend/base/default/images/reload.png"
                                             alt="Reload captcha" onclick="$('user_create').captcha.refresh(this)">
                                        <img id="user_create" class="captcha-img"
                                             src="http://ultimo.infortis-themes.com/demo/media/captcha/base/9ab42ea0a4e66593c976d1c09ec49776.png"
                                             height="50">
                                    </div>
                                    <!--                                    <script type="text/javascript">//<![CDATA[-->
                                    <!--                                        $('user_create').captcha = new Captcha('http://ultimo.infortis-themes.com/demo/default/captcha/refresh/', 'user_create');-->
                                    <!--                                        //]]></script>-->
                                </li>

                            </ul>


                        </div>
                        <div class="buttons-set">
                            <p class="required">* Required Fields</p>
                            <p class="back-link"><a
                                        href="http://ultimo.infortis-themes.com/demo/default/customer/account/login/"
                                        class="back-link">
                                    <small>«</small>
                                    Back</a></p>
                            <button type="submit" title="Submit" class="button"><span><span>Submit</span></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="postscript"></div>
    </div>
</div>