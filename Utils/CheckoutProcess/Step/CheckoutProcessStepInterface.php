<?php


namespace Module\Ekom\Utils\CheckoutProcess\Step;


use Module\Ekom\Utils\CheckoutProcess\CheckoutProcessInterface;

/**
 * Order of calls of this class' methods (synopsis):
 * --------------------------------------
 * - prepare
 * - isValid
 * - ?isPostedSuccessfully
 *          This method is called, except for one case:
 *          if the "non last" step's form is successfully posted, then the "next step"'s  isPostedSuccessfully
 *          method is not called.
 *
 *          After a successful isPostedSuccessfully method, every step gets the opportunity to update its
 *          state (active/inactive) with updateState method.
 *
 *          When a step is inactive, it's not displayed in the view at all.
 *
 * - getModel
 *
 *
 *
 *
 *
 */
interface CheckoutProcessStepInterface
{


    public function prepare(array $context);

    public function isValid();


    /**
     * Means that the step has just been clicked.
     * Generally, you won't do anything.
     *
     * But imagine your step is composed of 3 substeps,
     * then you could say that when the bar of your step is clicked,
     * you go back to substep 1 (instead of staying on the current substep).
     *
     *
     * @return void
     */
    public function click();

    /**
     *
     * @param array $context : the array representing the posted data.
     *          Hint: by default, it's array_replace($_GET, $_POST, $_FILES)
     *          Plus, it also contains the following:
     *          - shop_id
     *          - lang_id
     *          - currency_id
     *
     *
     *
     * @return bool
     */
    public function isPostedSuccessfully(CheckoutProcessInterface $cp, array $context);


    public function getModel();

    /**
     * The first time the CheckoutProcess displays the steps,
     * it will SELECT the first non skipped step.
     *
     * You can use this method and return true if you want to prevent
     * your step from being SELECTED.
     *
     * This can be useful for instance for a login step, where if the user
     * is already connected you don't need to display the login form...
     *
     *
     * @return mixed
     */
    public function isSkipped();

    //--------------------------------------------
    // ACTIVE/NOT ACTIVE
    // allow us to dynamically disable/re-enable steps
    //--------------------------------------------
//    public function updateState();
//
//    /**
//     * @return bool
//     */
//    public function isActive();
}