<?php

use Kamille\Mvc\HtmlPageHelper\HtmlPageHelper;


HtmlPageHelper::css("/theme/lee/css/widget.css");


?>


<div class="widget widget-login window2 widget-standard widget-hero">
    <h1>Login or Create an Account</h1>
    <div class="columns two-columns">
        <div class="column-50">
            <h2 class="title-underline">New Customers</h2>
            <div class="block">
                <p>
                    By creating an account with our store, you will be able to move through the checkout process faster,
                    store multiple shipping addresses, view and track your orders in your account and more.
                </p>
            </div>
            <div class="buttons-set">
                <a class="button" href="#">Create an Account</a>
            </div>
        </div>
        <div class="column-50">
            <h2 class="title-underline">Registered Customers</h2>
            <form>
                <div class="block">
                    <p class="mb20">If you have an account with us, please log in.</p>


                    <ul class="form-list">
                        <li>
                            <label for="email" class="required"><em>*</em>Email Address</label>
                            <div class="input-box">
                                <input name="username" value="" id="email" class="input-text" title="Email Address"
                                       type="text">
                            </div>
                        </li>
                        <li>
                            <label for="pass" class="required"><em>*</em>Password</label>
                            <div class="input-box">
                                <input name="password" class="input-text" id="pass" title="Password" type="password">
                            </div>
                        </li>
                    </ul>
                    <p class="required">* Required Fields</p>
                </div>
                <div class="buttons-set f-between">
                    <button type="submit" class="button" title="Login" name="send" id="send2">Login</button>
                    <a href="#" class="f-left">Forgot Your Password?</a>
                </div>
            </form>
        </div>
    </div>
</div>

