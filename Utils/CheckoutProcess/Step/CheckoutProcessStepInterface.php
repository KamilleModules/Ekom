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