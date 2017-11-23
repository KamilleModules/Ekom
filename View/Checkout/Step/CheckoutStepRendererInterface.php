<?php


namespace Module\Ekom\View\Checkout\Step;


interface CheckoutStepRendererInterface
{
    /**
     * Display the html corresponding to the step.
     *
     * @param array $stepItem , defined at top of CheckoutPageUtil (Module\Ekom\Utils\Checkout)
     * in the checkoutPageModel section.
     *
     * @return void
     */
    public function render(array $stepItem);
}
