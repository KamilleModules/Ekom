<?php


namespace Module\Ekom\SokoForm;


use Kamille\Architecture\Registry\ApplicationRegistry;
use SokoForm\Form\SokoForm;

class EkomSokoForm extends SokoForm
{
    public function __construct()
    {
        parent::__construct();
        $this->setValidationRulesLang(ApplicationRegistry::get("ekom.lang_iso"));
    }


}