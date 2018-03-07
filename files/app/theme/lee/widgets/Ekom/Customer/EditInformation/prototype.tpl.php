<div class="my-account"><div class="page-title">
        <h1>Edit Account Information</h1>
    </div>
    <form action="http://ultimo.infortis-themes.com/demo/default/customer/account/editPost/" method="post" id="form-validate" autocomplete="off">
        <div class="fieldset">
            <input name="form_key" type="hidden" value="n0EMo59BFeaaoJ1m" />
            <h2 class="legend">Account Information</h2>
            <ul class="form-list">
                <li class="fields">
                    <div class="customer-name-middlename">
                        <div class="field name-firstname">
                            <label for="firstname" class="required"><em>*</em>First Name</label>
                            <div class="input-box">
                                <input type="text" id="firstname" name="firstname" value="pou" title="First Name" maxlength="255" class="input-text required-entry"  />
                            </div>
                        </div>
                        <div class="field name-lastname">
                            <label for="lastname" class="required"><em>*</em>Last Name</label>
                            <div class="input-box">
                                <input type="text" id="lastname" name="lastname" value="pou" title="Last Name" maxlength="255" class="input-text required-entry"  />
                            </div>
                        </div>
                    </div>
                </li>
                <li>
                    <label for="email" class="required"><em>*</em>Email Address</label>
                    <div class="input-box">
                        <input type="text" name="email" id="email" value="poupou@pou.com" title="Email Address" class="input-text required-entry validate-email" />
                    </div>
                </li>
                <li>
                    <label for="current_password" class="required"><em>*</em>Current Password</label>
                    <div class="input-box">
                        <!-- This is a dummy hidden field to trick firefox from auto filling the password -->
                        <input type="text" class="input-text no-display" name="dummy" id="dummy" />
                        <input type="password" title="Current Password" class="input-text required-entry" name="current_password" id="current_password" />
                    </div>
                </li>
                <li class="control">
                    <input type="checkbox" name="change_password" id="change_password" value="1" onclick="setPasswordForm(this.checked)" title="Change Password" class="checkbox" /><label for="change_password">Change Password</label>
                </li>
            </ul>
        </div>
        <div class="fieldset" style="display:none;">
            <h2 class="legend">Change Password</h2>
            <ul class="form-list">
                <li class="fields">
                    <div class="field">
                        <label for="password" class="required"><em>*</em>New Password</label>
                        <div class="input-box">
                            <input type="password" title="New Password" class="input-text required-entry validate-password" name="password" id="password" />
                        </div>
                    </div>
                    <div class="field">
                        <label for="confirmation" class="required"><em>*</em>Confirm New Password</label>
                        <div class="input-box">
                            <input type="password" title="Confirm New Password" class="input-text required-entry validate-cpassword" name="confirmation" id="confirmation" />
                        </div>
                    </div>
                </li>
            </ul>
        </div>
        <div class="buttons-set">
            <p class="required">* Required Fields</p>
            <p class="back-link"><a href="http://ultimo.infortis-themes.com/demo/default/customer/account/"><small>&laquo; </small>Back</a></p>
            <button type="submit" title="Save" class="button"><span><span>Save</span></span></button>
        </div>
    </form>
    <script type="text/javascript">
        //<![CDATA[
        var dataForm = new VarienForm('form-validate', true);
        function setPasswordForm(arg){
            $('password').up('.fieldset')[arg ? 'show': 'hide']();
        }

        //]]>
    </script></div>