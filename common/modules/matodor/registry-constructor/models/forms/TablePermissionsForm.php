<?php

namespace Matodor\RegistryConstructor\models\forms;

use Matodor\RegistryConstructor\models\TablePermissions;
use Yii;
use yii\helpers\ArrayHelper;
use yii\rbac\Item;

/**
 * @property-read array|string[] $roles
 */
class TablePermissionsForm extends TablePermissions
{
    /**
     * @var string
     */
    public $uid;

    public function __construct($config = [])
    {
        $this->uid = $config['uid'] ?? uniqid('f');
        parent::__construct($config);
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[static::SCENARIO_DEFAULT][] = '!registry_table_id';
        $scenarios[static::SCENARIO_DEFAULT][] = '!special_role';

        return $scenarios;
    }

    public function formName()
    {
        return TableForm::instance()->formName() . "[editPermissions][{$this->uid}]";
    }

    /**
     * @return array
     */
    public function getRolesSelectData()
    {
        return [
            TablePermissions::SPECIAL_ROLE_AUTH => 'Авторизованные пользователи',
            TablePermissions::SPECIAL_ROLE_GUEST => 'Гости',
        ] + ArrayHelper::map(Yii::$app->authManager->getItems(Item::TYPE_ROLE), 'name', function ($item) {
            return empty($item->description)
                ? $item->name
                : $item->name . ' (' . $item->description . ')';
        });
    }
}
