<?php


namespace Module\Ekom\View\Checkout;


use Module\Ekom\Exception\EkomException;
use Module\Ekom\View\Checkout\Step\CheckoutStepRendererInterface;

class CheckoutTunnelRenderer
{

    /**
     * @var CheckoutStepRendererInterface[]
     */
    private $renderers;

    public function __construct()
    {
        $this->renderers = [];
    }


    public static function create()
    {
        return new static();
    }


    public function addRenderer($name, CheckoutStepRendererInterface $renderer)
    {
        $this->renderers[$name] = $renderer;
        return $this;
    }

    /**
     * @param array $checkoutPageModel , defined at top of CheckoutPageUtil (Module\Ekom\Utils\Checkout)
     * @throws EkomException
     */
    public function render(array $checkoutPageModel)
    {
        $steps = $checkoutPageModel['steps'];
        ?>
        <div class="checkout-tunnel">
            <?php foreach ($steps as $stepName => $stepItem): ?>
                <?php
                $renderer = $this->getStepRenderer($stepName);
                if (false === $renderer) {
                    throw new EkomException("CheckoutTunnelRenderer: Renderer not found for step $stepName");
                }
                $renderer->render($stepItem);
                ?>
            <?php endforeach; ?>
        </div>
        <?php
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    protected function getStepRenderer($stepName)
    {
        if (array_key_exists($stepName, $this->renderers)) {
            return $this->renderers[$stepName];
        }
        return false;
    }
}



