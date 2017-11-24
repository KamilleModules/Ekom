<?php


namespace Module\Ekom\Utils\CheckoutProcess\Step;


use Module\Ekom\Utils\CheckoutProcess\CheckoutProcessInterface;

/**
 * Order of calls of this class' methods (synopsis):
 * --------------------------------------
 * - isValid
 * - isPostedSuccessfully (like right now)
 *      This method actually asks to:
 *          - check whether or not this (step) form was posted
 *          - if it's posted, is it successful (return the result)
 *
 *      So, yes, this method handles the form submission.
 *
 * - getModel
 *
 *
 */
interface CheckoutProcessStepInterface
{


    public function isValid();

    /**
     * @return bool
     */
    public function isPostedSuccessfully(CheckoutProcessInterface $cp, array $context);


    public function getModel();
}