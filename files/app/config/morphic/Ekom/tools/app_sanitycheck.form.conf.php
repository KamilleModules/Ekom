<?php


use Bat\UriTool;
use Kamille\Architecture\Controller\Exception\ClawsHttpResponseException;
use Kamille\Architecture\Response\Web\RedirectResponse;
use Module\Ekom\Back\Util\ApplicationSanityCheck\ApplicationSanityCheckUtil;
use SokoForm\Control\SokoChoiceControl;
use SokoForm\Form\SokoForm;
use SokoForm\Form\SokoFormInterface;


$severityValue = 0;
$severityValues = [
    100 => 'critical',
];


$conf = [
    //--------------------------------------------
    // FORM WIDGET
    //--------------------------------------------
    'title' => "Carrier",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-tools-app_sanitycheck")
        ->addControl(SokoChoiceControl::create()
            ->setName("severity")
            ->setLabel('Minimum Severity')
            ->setValue($severityValue)
            ->setChoices($severityValues)
        )
    ,
    'feed' => function (SokoFormInterface $form, array $ric) {
    },
    'process' => function ($fData, SokoFormInterface $form) {
        $severity = $fData['severity'];
        $errors = ApplicationSanityCheckUtil::check($severity);
        if ($errors) {
            throw ClawsHttpResponseException::create()->setHttpResponse(RedirectResponse::create(UriTool::uri(null, [], false)));
        }
    },
    //--------------------------------------------
    // to fetch values
    'ric' => [
        'id',
    ],
];