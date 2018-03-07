<?php


use Module\Ekom\Api\Layer\CategoryLayer;
use Module\Ekom\Api\Layer\LangLayer;
use Module\Ekom\Api\Object\CategoryLang;
use Module\Ekom\Back\User\EkomNullosUser;
use Module\Ekom\Exception\EkomException;
use QuickPdo\QuickPdo;
use QuickPdo\QuickPdoExceptionTool;
use SokoForm\Control\SokoChoiceControl;
use SokoForm\Control\SokoInputControl;
use SokoForm\Form\SokoForm;
use SokoForm\Form\SokoFormInterface;
use SokoForm\ValidationRule\SokoNotEmptyValidationRule;

if (
    array_key_exists("category_id", $context) &&
    array_key_exists("category_name", $context)
) {

    $category_id = $context['category_id'];
    $category_name = $context['category_name'];
    $contextLangId = (int)EkomNullosUser::getEkomValue("lang_id");

    $langId = (array_key_exists("lang_id", $_GET)) ? (int)$_GET['lang_id'] : 0;


    $isUpdate = (0 !== $langId);
    if (0 === $langId) {
        $langId = $contextLangId;
    }


    //--------------------------------------------
    // HYBRID CURRENCY CONTROL
    //--------------------------------------------
    $langItems = LangLayer::getLangItems();
    $langControl = SokoChoiceControl::create()
        ->setChoices($langItems)
        ->setName("lang_id")
        ->setLabel('Lang id')
        ->setValue($langId);


    if ($isUpdate) {
        $langControl->setProperties([
            'readonly' => true,
        ]);
    }


    //--------------------------------------------
    // CONF
    //--------------------------------------------
    $conf = [
        //--------------------------------------------
        // FORM WIDGET
        //--------------------------------------------
        'title' => "Shop category \"" . $category_name . "\" translation",
        //--------------------------------------------
        // SOKO FORM
        'form' => SokoForm::create()
            ->setAction("?" . http_build_query($_GET))
            ->setName("soko-form-shop_category_lang")
            ->addControl(SokoInputControl::create()
                ->setName("category_id")
                ->setLabel('Category id')
                ->setProperties([
                    'readonly' => true,
                ])
                ->setValue($category_id)
            )
            ->addControl($langControl)
            ->addControl(SokoInputControl::create()
                ->setName("label")
                ->setLabel('Label')
            )
            ->addControl(SokoInputControl::create()
                ->setName("description")
                ->setType("textarea")
                ->setLabel('Description')
            )
            ->addControl(SokoInputControl::create()
                ->setName("slug")
                ->setLabel('Slug')
            )
            ->addControl(SokoInputControl::create()
                ->setName("meta_title")
                ->setLabel('Meta title')
            )
            ->addControl(SokoInputControl::create()
                ->setName("meta_description")
                ->setType("textarea")
                ->setLabel('Meta description')
            )
            ->addControl(SokoInputControl::create()
                ->setName("meta_keywords")
                ->setType("textarea")
                ->setLabel('Meta keywords')
            )
            ->addValidationRule("label", SokoNotEmptyValidationRule::create())
            ->addValidationRule("slug", SokoNotEmptyValidationRule::create())
        ,
        'feed' => function (SokoFormInterface $form, array $ric) use ($category_id, $langId) {
            if (null !== $category_id) {
                $markers = [];

                $category_id = (int)$category_id;
                $q = "select *
from ek_category_lang 
where category_id=$category_id
and lang_id=$langId
";
                $row = QuickPdo::fetch("$q", $markers);
            } else {
                $row = ['category_id' => $category_id];
            }
            $form->inject($row);
        },
        'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $category_id, $langId) {

            if (true === $isUpdate) { // update
                CategoryLang::getInst()->update($fData, [
                    "category_id" => $fData['category_id'],
                    "lang_id" => $fData['lang_id'],
                ]);

                $form->addNotification("La traduction de catégorie a bien été mise à jour", "success");
            } else {

                $slug = $fData['slug'];
                if (true === CategoryLayer::checkSlugUnique($category_id, $langId, $slug)) {


                    try {
                        CategoryLang::getInst()->create($fData);
                        $form->addNotification("La traduction de catégorie a bien été ajoutée", "success");
                    } catch (\PDOException $e) {
                        if (QuickPdoExceptionTool::isDuplicateEntry($e)) {
                            $form->addNotification("Cette entrée existe déjà", "warning");
                        } else {
                            throw $e;
                        }
                    }
                } else {
                    $form->addNotification("Le slug choisi existe déjà pour la catégorie et la langue choisie", "warning");
                }
            }

            return false;
        },
        //--------------------------------------------
        // to fetch values
        'ric' => [
            'category_id',
            'lang_id',
        ],
    ];


} else {
    throw new EkomException("Some variables not found in the given context");
}