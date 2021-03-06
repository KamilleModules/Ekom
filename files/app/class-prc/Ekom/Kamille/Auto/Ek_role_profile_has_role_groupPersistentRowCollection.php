<?php



namespace Prc\Ekom\Kamille\Auto;



use FormModel\Validation\ControlTest\WithFields\RequiredControlTest;
use Module\NullosAdmin\FormModel\Control\SqlQuerySelectControl;

use FormModel\FormModel;
use FormModel\Validation\ControlsValidator\ControlsValidator;
use Module\NullosAdmin\PersistentRowCollection\NullosQuickPdoPersistentRowCollection;


class Ek_role_profile_has_role_groupPersistentRowCollection extends NullosQuickPdoPersistentRowCollection
{
    public function __construct()
    {
        parent::__construct();
        $this->setTable("kamille.ek_role_profile_has_role_group");
        $this->fields = '
ek_role_profile_has_role_group.role_profile_id as `ek_role_profile_has_role_group.role_profile_id`,
ek_role_profile_has_role_group.role_group_id as `ek_role_profile_has_role_group.role_group_id`,
ek_role_profile.label as `ek_role_profile.label`,
ek_role_group.label as `ek_role_group.label`
';
        $this->query = '
SELECT
%s
FROM kamille.ek_role_profile_has_role_group
inner join kamille.ek_role_group on kamille.ek_role_group.id=ek_role_profile_has_role_group.role_group_id
inner join kamille.ek_role_profile on kamille.ek_role_profile.id=ek_role_profile_has_role_group.role_profile_id
';
    }


    public function getRic()
    {
        return [
    'role_profile_id',
    'role_group_id',
];
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    protected function decorateFormModelValidator(ControlsValidator $validator)
    {
        $validator
			->setTests("role_profile_id", "role_profile_id", [
                RequiredControlTest::create(),
            ])
			->setTests("role_group_id", "role_group_id", [
                RequiredControlTest::create(),
            ]);

    }

    protected function decorateFormModel(FormModel $model)
    {
        $model
            ->addControl("role_profile_id", SqlQuerySelectControl::create()
                //->multiple()
                ->query('select id, label from kamille.ek_role_profile')
                 
                ->label("role_profile_id")
                ->name("role_profile_id")
            )
            ->addControl("role_group_id", SqlQuerySelectControl::create()
                //->multiple()
                ->query('select id, label from kamille.ek_role_group')
                 
                ->label("role_group_id")
                ->name("role_group_id")
            );

    }

    protected function getAutoIncrementedColumn()
    {
        return null;
    }
}