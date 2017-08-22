<?php


namespace Module\Ekom\StepFormBuilder\Step;


use Core\Services\A;
use StepFormBuilder\Step\OnTheFlyFormStep;

class LoginStep extends OnTheFlyFormStep
{
    public function __construct()
    {
        parent::__construct();
        $this->setForm(A::getOnTheFlyForm("Ekom:CheckoutLogin"));
    }

}
