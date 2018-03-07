<div class="column-main">
    <div class="page-title">
        <h1>Forgot Your Password?</h1>
    </div>
    <form action="http://ultimo.infortis-themes.com/demo/default/customer/account/forgotpasswordpost/" method="post" id="form-validate">
        <div class="fieldset">
            <h2 class="legend">Retrieve your password here</h2>
            <p>Please enter your email address below. You will receive a link to reset your password.</p>
            <ul class="form-list">
                <li>
                    <label for="email_address" class="required"><em>*</em>Email Address</label>
                    <div class="input-box">
                        <input type="text" name="email" alt="email" id="email_address" class="input-text required-entry validate-email" value="" />
                    </div>
                </li>

                <li id="captcha-input-box-user_forgotpassword">
                    <label for="captcha_user_forgotpassword" class="required"><em>*</em>Please type the letters below</label>
                    <div class="input-box captcha">
                        <input name="captcha[user_forgotpassword]" type="text" class="input-text required-entry" id="captcha_user_forgotpassword" />
                    </div>
                </li>
                <li>
                    <div class="captcha-image" id="captcha-image-box-user_forgotpassword">
                        <img id="captcha-reload" class="captcha-reload" src="http://ultimo.infortis-themes.com/demo/skin/frontend/base/default/images/reload.png" alt="Reload captcha" onclick="$('user_forgotpassword').captcha.refresh(this)">
                        <img id="user_forgotpassword" class="captcha-img" height="50" src="http://ultimo.infortis-themes.com/demo/media/captcha/base/b1c66c2ac5771a9246c650c56395f4d0.png"/>
                    </div>
                    <script type="text/javascript">//<![CDATA[
                        $('user_forgotpassword').captcha = new Captcha('http://ultimo.infortis-themes.com/demo/default/captcha/refresh/', 'user_forgotpassword');
                        //]]></script>
                </li>

            </ul>
        </div>
        <div class="buttons-set">
            <p class="required">* Required Fields</p>
            <p class="back-link"><a href="http://ultimo.infortis-themes.com/demo/default/customer/account/login/"><small>&laquo; </small>Back to Login</a></p>
            <button type="submit" title="Submit" class="button"><span><span>Submit</span></span></button>
        </div>
    </form>
    <script type="text/javascript">
        //<![CDATA[
        var dataForm = new VarienForm('form-validate', true);
        //]]>
    </script>
</div>