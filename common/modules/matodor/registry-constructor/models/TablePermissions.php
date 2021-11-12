<?php

namespace Matodor\RegistryConstructor\models;

use Matodor\Common\components\Helper;
use Matodor\Common\models\BaseModel;
use Matodor\RegistryConstructor\queries\TablePermissionsQuery;
use Matodor\RegistryConstructor\queries\TableQuery;
use Matodor\RegistryConstructor\traits\HasModulePropertiesTrait;
use Yii;
use yii\base\InvalidConfigException;

/**
 * Class TablePermissions
 * @package Matodor\RegistryConstructor\models
 *
 * @property integer $id
 * @property integer $registry_table_id
 * @property string|null $special_role
 * @property string|null $role
 * @property boolean $can_add_records
 * @property boolean $can_view_all_records
 * @property boolean $can_view_self_records
 * @property boolean $can_edit_self_records
 * @property boolean $can_edit_all_records
 * @property boolean $can_delete_self_records
 * @property boolean $can_delete_all_records
 *
 * @property-read null|string $mainRole
 *
 * @property-read Table $table
 */
class TablePermissions extends BaseModel
{
    use HasModulePropertiesTrait;

    public const SPECIAL_ROLE_GUEST = '?';
    public const SPECIAL_ROLE_AUTH = '@';

    public const CAN_ADD_RECORDS = 'can_add_records';
    public const CAN_VIEW_ALL_RECORDS = 'can_view_all_records';
    public const CAN_VIEW_SELF_RECORDS = 'can_view_self_records';
    public const CAN_EDIT_SELF_RECORDS = 'can_edit_self_records';
    public const CAN_EDIT_ALL_RECORDS = 'can_edit_all_records';
    public const CAN_DELETE_SELF_RECORDS = 'can_delete_self_records';
    public const CAN_DELETE_ALL_RECORDS = 'can_delete_all_records';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%registry_table_permissions}}';
    }

    /**
     * @return TablePermissionsQuery
     * @throws InvalidConfigException
     */
    public static function find()
    {
        return Yii::createObject(TablePermissionsQuery::class, [get_called_class()]);
    }

    public static function getPermissionsList()
    {
        return [
            static::CAN_ADD_RECORDS,
            static::CAN_VIEW_ALL_RECORDS,
            static::CAN_VIEW_SELF_RECORDS,
            static::CAN_EDIT_ALL_RECORDS,
            static::CAN_EDIT_SELF_RECORDS,
            static::CAN_DELETE_ALL_RECORDS,
            static::CAN_DELETE_SELF_RECORDS,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [static::getPermissionsList(), 'default', 'value' => false],
            [static::getPermissionsList(), 'boolean'],

            ['registry_table_id', 'required', 'except' => [static::SCENARIO_SEARCH]],
            ['special_role', 'string', 'max' => 1],
            ['role', 'string', 'max' => 64],
            ['registry_table_id', 'exist', 'targetClass' => Table::class, 'targetAttribute' => 'id'],
        ];
    }

    /**
     * {@inheritdoc}
     * @noinspection DuplicatedCode
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Идентификатор',
            'special_role' => 'Специальная роль',
            'role' => 'Роль',
            'registry_table_id' => 'ID реестра',
            static::CAN_ADD_RECORDS => 'Доступ к добавлению записей в таблицу',
            static::CAN_VIEW_ALL_RECORDS => 'Доступ к просмотру всех записей в таблице',
            static::CAN_VIEW_SELF_RECORDS => 'Доступ к просмотру своих записей в таблице',
            static::CAN_EDIT_ALL_RECORDS => 'Доступ к редактированию всех записей в таблице',
            static::CAN_EDIT_SELF_RECORDS => 'Доступ к редактированию своих записей в таблице',
            static::CAN_DELETE_ALL_RECORDS => 'Доступ к удалению всех записей в таблице',
            static::CAN_DELETE_SELF_RECORDS => 'Доступ к удалению своих записей в таблице',
        ];
    }

    /**
     * @return TableQuery
     * @noinspection PhpUnused
     * @noinspection PhpIncompatibleReturnTypeInspection
     * @noinspection PhpReturnDocTypeMismatchInspection
     */
    public function getTable()
    {
        return $this->hasOne(Table::class, ['id' => 'registry_table_id']);
    }

    public function afterFind()
    {
        parent::afterFind();

        if ($this->special_role !== null) {
            $this->role = $this->special_role;
        }
    }

    public function afterValidate()
    {
        parent::afterValidate();

        if (in_array($this->role, [
            static::SPECIAL_ROLE_GUEST,
            static::SPECIAL_ROLE_AUTH,
        ])) {
            $this->special_role = $this->role;
        }

        if (Helper::isEmpty($this->role)
            && Helper::isEmpty($this->special_role)
        ) {
            $this->addError('role', 'Выберите роль или примените ко всем ролям');
            $this->addError('special_role', 'Выберите роль или примените ко всем ролям');
        }

        if ($this->special_role === static::SPECIAL_ROLE_GUEST) {
            if ($this->can_add_records) {
                $this->addError(static::CAN_ADD_RECORDS, 'Гостям запрещено добавлять записи в таблицу');
            }

            if ($this->can_edit_self_records) {
                $this->addError(static::CAN_EDIT_SELF_RECORDS, 'Гостям запрещено редактировать записи');
            }

            if ($this->can_edit_all_records) {
                $this->addError(static::CAN_EDIT_ALL_RECORDS, 'Гостям запрещено редактировать записи');
            }

            if ($this->can_delete_self_records) {
                $this->addError(static::CAN_DELETE_SELF_RECORDS, 'Гостям запрещено удалять записи');
            }

            if ($this->can_delete_all_records) {
                $this->addError(static::CAN_DELETE_ALL_RECORDS, 'Гостям запрещено удалять записи');
            }
        }
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        if (in_array($this->role, [
            static::SPECIAL_ROLE_GUEST,
            static::SPECIAL_ROLE_AUTH,
        ])) {
            $this->special_role = $this->role;
            $this->role = null;
        }

        return true;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        $this->invalidateCache();
    }

    public function afterDelete()
    {
        parent::afterDelete();

        $this->invalidateCache();
    }

    public function invalidateCache()
    {
        $this->module->cache->delete(Table::generateCacheKey($this->registry_table_id, 'permissions'));
    }

    /**
     * @return string|null
     * @noinspection PhpUnused
     */
    public function getMainRole()
    {
        return $this->special_role ?? $this->role;
    }

    /**
     * @param $permissionName
     *
     * @return bool
     */
    public function isAllowed($permissionName)
    {
        return $this->getAttribute($permissionName) == true;
    }
}
