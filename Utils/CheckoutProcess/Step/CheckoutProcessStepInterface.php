<?php


namespace Module\Ekom\Utils\CheckoutProcess\Step;


use Module\Ekom\Utils\CheckoutProcess\CheckoutProcessInterface;

interface CheckoutProcessStepInterface
{


    /**
     * Use this method to:
     *
     * - prepare data persistency (if a user fills a step, goes to another page, then comes back on the checkout page)
     *              use cp.get/set for this purpose
     * - post the form (assumed created during the constructor) and memorize the result (you will
     *              return this result using the isValid method)
     *
     *
     */
    public function prepare(CheckoutProcessInterface $cp, array $context);

    public function isValid();

    public function getModel();
}