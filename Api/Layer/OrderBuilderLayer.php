<?php


namespace Module\Ekom\Api\Layer;


use Core\Services\X;
use Module\Ekom\Utils\OrderBuilder\Collection\OrderBuilderCollectionInterface;
use StepFormBuilder\Step\OnTheFlyFormStep;
use StepFormBuilder\StepFormBuilder;

class OrderBuilderLayer
{

    public function get($name, $throwEx = true, $default = null)
    {
        /**
         * @var $col OrderBuilderCollectionInterface
         */
        $col = X::get("Ekom_OrderBuilderCollection");
        return $col->get($name, $throwEx, $default);
    }


//    public function getLoginForm(){
//
//    }
//
//    public function getStepFormBuilder()
//    {
//        /**
//         * @var $col OrderBuilderCollectionInterface
//         */
//        $col = X::get("Ekom_CheckoutFormBuilder");
//        return $col->get($name, $throwEx, $default);
//
//
//        $builder = new StepFormBuilder();
//        $builder->registerStep('login', OnTheFlyFormStep::create()->setForm(OnTheFlyForm::create([
//            "login",
//        ], 'login-key')
//            ->setValidationRules([
//                'login' => ['required'],
//            ])
//        ));
//
//
//        $builder->registerStep('training1', OnTheFlyFormStep::create()->setForm(OnTheFlyForm::create([
//            "motivation",
//        ], 'motivation-key')
//            ->setValidationRules([
//                'motivation' => ['required'],
//            ])
//        ));
//        $builder->registerStep('training2', OnTheFlyFormStep::create()->setForm(OnTheFlyForm::create([
//            "provenance",
//        ], 'provenance-key')
//            ->setValidationRules([
//                'provenance' => ['required'],
//            ])
//        ));
//        $builder->registerStep('training3', OnTheFlyFormStep::create()->setForm(OnTheFlyForm::create([
//            "explanations",
//        ], 'explanations-key')
//            ->setValidationRules([
//                'explanations' => ['required'],
//            ])
//        ));
//
//
//        $builder->registerStep('shipping', OnTheFlyFormStep::create()->setForm(OnTheFlyForm::create([
//            "shipping_mode",
//        ], 'shipping-mode-key')
//            ->setValidationRules([
//                'shipping_mode' => ['required'],
//            ])
//        ));
//
//        $builder->registerStep('payment', OnTheFlyFormStep::create()->setForm(OnTheFlyForm::create([
//            "payment_mode",
//        ], 'payment-mode-key')->setValidationRules([
//            'payment_mode' => ['required'],
//        ])));
//
//        return $builder;
//    }


}
