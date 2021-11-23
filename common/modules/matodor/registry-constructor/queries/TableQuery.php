<?php

namespace Matodor\RegistryConstructor\queries;

use Matodor\RegistryConstructor\models\TablePermissions;
use Yii;
use yii\base\InvalidArgumentException;
use yii\base\InvalidConfigException;
use yii\di\Instance;
use yii\rbac\DbManager;

class TableQuery extends \yii\db\ActiveQuery
{
    /**
     * @var string|DbManager
     */
    protected $authManager = 'authManager';

    public function init()
    {
        parent::init();

        $this->authManager = Instance::ensure($this->authManager, DbManager::class);
    }

    /**
     * @param $type
     *
     * @return TableQuery
     */
    public function isType($type)
    {
        return $this->andWhere(['type' => $type]);
    }

    /**
     * @param bool $state
     *
     * @return TableQuery
     */
    public function isVisibleInMenu(bool $state = true)
    {
        return $this->andWhere(['visible_in_menu' => $state]);
    }

    /**
     * @param $token
     *
     * @return TableQuery
     */
    public function andWhereToken($token)
    {
        return $this->andWhere(['token' => $token]);
    }

    /**
     * @param int|string|null $userId
     *
     * @return TableQuery
     * @throws InvalidArgumentException|InvalidConfigException
     */
    public function andWhereHasAnyPermission($userId = null)
    {
        $whereOrAnyPermission = [
            'OR',
            ['=', TablePermissions::CAN_ADD_RECORDS, true],
            ['=', TablePermissions::CAN_VIEW_ALL_RECORDS, true],
            ['=', TablePermissions::CAN_VIEW_SELF_RECORDS, true],
            ['=', TablePermissions::CAN_EDIT_ALL_RECORDS, true],
            ['=', TablePermissions::CAN_EDIT_SELF_RECORDS, true],
            ['=', TablePermissions::CAN_DELETE_ALL_RECORDS, true],
            ['=', TablePermissions::CAN_DELETE_SELF_RECORDS, true],
        ];

        $wherePermissions = [
            'OR',
            [
                'AND',
                ['IN', 'special_role', ['?', '@']],
                $whereOrAnyPermission,
            ],
        ];

        if ($userId === null) {
            $userId = Yii::$app->user->id;
        }

        if ($userId !== null) {
            $roles = array_keys($this->authManager->getRolesByUser($userId));

            if (count($roles) > 0) {
                $wherePermissions[] = [
                    'AND',
                    ['IN', 'role', $roles],
                    $whereOrAnyPermission,
                ];
            }
        }

        $queryPermissionsAlias = uniqid('t');
        $queryPermissions = TablePermissions::find()
            ->select(['registry_table_id'])
            ->groupBy(['registry_table_id'])
            ->where($wherePermissions);

        [$table, $tableAlias] = $this->getTableNameAndAlias();

        return $this->innerJoin([$queryPermissionsAlias => $queryPermissions],
            "$tableAlias.id = $queryPermissionsAlias.registry_table_id");
    }
}
