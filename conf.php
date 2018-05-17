<?php


use Module\Ekom\Utils\E;

$conf = array_replace([
    /**
     * The base uri where all product images are put/found.
     * This usually also defines the directory where images are (by prefixing /$app_dir/www in front of the uri)
     */
    'uriProductImageBaseDir' => "/img/products",
    /**
     * If true, an email is sent to the user and she/he must click the link inside to activate her/his account.
     * If false, the user account is created directly after the user successfully post the create account form.
     */
    'createAccountNeedValidation' => false,
    /**
     * If comment need validation (true by default),
     * a freshly inserted comment will not appear on the website,
     * the comment moderator will receive an email and will validate/not validate the comment.
     *
     * If the comment needs no validation, then it appears immediately on the website
     * once posted by the user.
     *
     * In both scenario, the user receives an email when the comment is posted on the website,
     * and/or rejected by the moderator.
     *
     */
    'commentNeedValidation' => false,
    'commentModeratorEmail' => "lingtalfi@gmail.com",
    /**
     * passwordRecovery
     * ==================
     * The number of seconds after which the password token (to create a new password) becomes invalid.
     * (the token is sent by email to the user when the user asks for her password recovery).
     */
    'passwordRecoveryNbSeconds' => 3 * 86400,
    'OnTheFlyFormValidatorMessageClass' => null,
    //--------------------------------------------
    // NIPPS
    //--------------------------------------------
    'nipp.category' => 20,
    //--------------------------------------------
    // GOOGLE MAP
    //--------------------------------------------
    'googleMapKey' => IC_GOOGLE_MAP_KEY,

    //--------------------------------------------
    // USER RESTRICTIONS
    //--------------------------------------------
    'maxUserAddresses' => 10,
    'safeUploadConfigFile' => "/myphp/leaderfit/leaderfit/class/SafeUploader/assets/example.config.php",
], E::cheaterConfig("Ekom"));