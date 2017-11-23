<?php


namespace Module\Ekom\Utils\CheckoutProcess\Step\Soko;


use Module\Ekom\Utils\CheckoutProcess\CheckoutProcessInterface;
use Module\Ekom\Utils\CheckoutProcess\Step\BaseCheckoutProcessStep;
use Module\Ekom\Utils\E;
use SokoForm\Control\SokoInputControl;
use SokoForm\Form\SokoForm;
use SokoForm\Form\SokoFormInterface;

class SokoLoginCheckoutProcessStep extends BaseCheckoutProcessStep
{
    /**
     * @var SokoFormInterface $form
     */
    private $form;

    public function __construct()
    {
        parent::__construct();
        $this->form = SokoForm::create()->addControl(SokoInputControl::create()
            ->setName("email")
            ->setLabel('Your email')
        );
    }

    public function prepare(CheckoutProcessInterface $cp, array $context)
    {
        $this->form->process(function () {
            // todo: connect the user
            a("connecting the user");
        });
    }

    public function isValid()
    {
        return E::userIsConnected();
    }

    public function getModel()
    {
        return $this->form->getModel();
    }
}