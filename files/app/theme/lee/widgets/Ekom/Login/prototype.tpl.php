<?php


use Module\Ekom\Utils\E;



?>
<div class="main container">
    <div class="inner-container">

        <div class="columns">
            <div class="column-main">
                <div class="account-login clearer">
                    <div class="page-title">
                        <h1>Login or Create an Account</h1>
                    </div>
                    <form action="http://ultimo.infortis-themes.com/demo/default/customer/account/loginPost/"
                          method="post"
                          id="login-form">
                        <input name="form_key" type="hidden" value="eeXCxhq4ZncSzOAq"/>
                        <div class="new-users grid12-6">
                            <div class="content">
                                <h2>New Customers</h2>
                                <p>By creating an account with our store, you will be able to move through the checkout
                                    process
                                    faster, store multiple shipping addresses, view and track your orders in your
                                    account
                                    and
                                    more.</p>
                            </div>
                            <div class="buttons-set">
                                <button type="button" title="Create an Account" class="button"
                                        onclick="window.location='<?php echo E::link("Ekom_createAccount"); ?>';">
                                    <span><span>Create an Account</span></span>
                                </button>
                            </div>
                        </div>
                        <div class="registered-users grid12-6">
                            <div class="content">
                                <h2>Registered Customers</h2>
                                <p>If you have an account with us, please log in.</p>
                                <ul class="form-list">
                                    <li>
                                        <label for="email" class="required"><em>*</em>Email Address</label>
                                        <div class="input-box">
                                            <input type="text" name="login[username]" value="" id="email"
                                                   class="input-text required-entry validate-email"
                                                   title="Email Address"/>
                                        </div>
                                    </li>
                                    <li>
                                        <label for="pass" class="required"><em>*</em>Password</label>
                                        <div class="input-box">
                                            <input type="password" name="login[password]"
                                                   class="input-text required-entry validate-password" id="pass"
                                                   title="Password"/>
                                        </div>
                                    </li>
                                </ul>
                                <div id="window-overlay" class="window-overlay" style="display:none;"></div>
                                <div id="remember-me-popup" class="remember-me-popup" style="display:none;">
                                    <div class="remember-me-popup-head">
                                        <h3>What's this?</h3>
                                        <a href="#" class="remember-me-popup-close" title="Close">Close</a>
                                    </div>
                                    <div class="remember-me-popup-body">
                                        <p>Checking &quot;Remember Me&quot; will let you access your shopping cart on
                                            this
                                            computer
                                            when you are logged out</p>
                                        <div class="remember-me-popup-close-button a-right">
                                            <a href="#" class="remember-me-popup-close button"
                                               title="Close"><span>Close</span></a>
                                        </div>
                                    </div>
                                </div>
                                <p class="required">* Required Fields</p>
                            </div>
                            <div class="buttons-set">
                                <a href="<?php echo E::link("Ekom_forgotPassword"); ?>"
                                   class="f-left">Forgot Your Password?</a>
                                <button type="submit" onclick="window.location.href='<?php echo E::link("Ekom_customerDashboard"); ?>'; return false" class="button" title="Login" name="send" id="send2">
                                    <span><span>Login</span></span></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>