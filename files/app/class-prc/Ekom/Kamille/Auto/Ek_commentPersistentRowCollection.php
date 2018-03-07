<?php



namespace Prc\Ekom\Kamille\Auto;



use FormModel\Validation\ControlTest\WithFields\RequiredControlTest;
use Module\NullosAdmin\FormModel\Control\SqlQuerySelectControl;
use FormModel\Control\TextAreaControl;
use Module\NullosAdmin\FormModel\Control\DatetimePickerInputTextControl;
use Module\NullosAdmin\FormModel\Control\InputSwitchControl;

use FormModel\FormModel;
use FormModel\Validation\ControlsValidator\ControlsValidator;
use Module\NullosAdmin\PersistentRowCollection\NullosQuickPdoPersistentRowCollection;


class Ek_commentPersistentRowCollection extends NullosQuickPdoPersistentRowCollection
{
    public function __construct()
    {
        parent::__construct();
        $this->setTable("kamille.ek_comment");
        $this->fields = '
ek_comment.id as `ek_comment.id`,
ek_user.email as `ek_user.email`,
ek_shop.label as `ek_shop.label`,
ek_comment.text as `ek_comment.text`,
ek_comment.date_creation as `ek_comment.date_creation`,
ek_comment.active as `ek_comment.active`
';
        $this->query = '
SELECT
%s
FROM kamille.ek_comment
inner join kamille.ek_shop on kamille.ek_shop.id=ek_comment.shop_id
inner join kamille.ek_user on kamille.ek_user.id=ek_comment.user_id
';
    }


    public function getRic()
    {
        return [
    'id',
];
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    protected function decorateFormModelValidator(ControlsValidator $validator)
    {
        $validator
			->setTests("user_id", "user_id", [
                RequiredControlTest::create(),
            ])
			->setTests("shop_id", "shop_id", [
                RequiredControlTest::create(),
            ]);

    }

    protected function decorateFormModel(FormModel $model)
    {
        $model
            ->addControl("user_id", SqlQuerySelectControl::create()
                //->multiple()
                ->query('select id, email from kamille.ek_user')
                 
                ->label("user_id")
                ->name("user_id")
            )
            ->addControl("shop_id", SqlQuerySelectControl::create()
                //->multiple()
                ->query('select id, label from kamille.ek_shop')
                 
                ->label("shop_id")
                ->name("shop_id")
            )
            ->addControl("text", TextAreaControl::create()
                ->label("text")
                ->name("text")
            )
            ->addControl("date_creation", DatetimePickerInputTextControl::create()
                ->injectJsConfigurationKey(['timePicker' => true])
                ->label("date_creation")
                ->name("date_creation")
            )
            ->addControl("active", InputSwitchControl::create()
                ->label("active")
                ->name("active")
                ->addHtmlAttribute("value", "1")
            );

    }

    protected function getAutoIncrementedColumn()
    {
        return 'id';
    }
}