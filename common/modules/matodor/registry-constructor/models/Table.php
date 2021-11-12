<?php

namespace Matodor\RegistryConstructor\models;

use Closure;
use Matodor\Common\models\BaseModel;
use Matodor\RegistryConstructor\Module;
use Matodor\RegistryConstructor\queries\TableFieldQuery;
use Matodor\RegistryConstructor\queries\TablePermissionsQuery;
use Matodor\RegistryConstructor\queries\TableQuery;
use Matodor\RegistryConstructor\queries\TableRecordQuery;
use Matodor\RegistryConstructor\traits\HasModulePropertiesTrait;
use Matodor\RegistryConstructor\widgets\RecordsSearch\Widget as RecordsSearchWiget;
use Matodor\RegistryConstructor\widgets\RecordsViewer\Widget as RecordsViewerWidget;
use Matodor\RegistryConstructor\widgets\RecordsViewerToolbar\Widget as RecordsViewerToolbarWidget;
use Yii;
use yii\base\Behavior;
use yii\base\Exception;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use yii\web\AssetBundle;
use yii\web\ForbiddenHttpException;

/**
 * @property integer $id
 * @property string $name
 * @property string $token Unique token, useful for identification table instead of using table name
 * @property string $inline_markup ! Deprecated
 * @property integer $type
 * @property boolean $visible_in_menu
 * @property integer $default_page_size
 *
 * @property string|RecordsViewerWidget $widget_class_search
 * @property string|RecordsViewerToolbarWidget $widget_class_toolbar
 * @property string|RecordsSearchWiget $widget_class_viewer
 * @property string|Behavior[] $table_behaviors
 * @property string|Behavior[] $records_behaviors
 * @property string|AssetBundle[] $records_assets
 *
 * @property-read TableField[] $fields
 * @property-read TableRecord[] $records
 * @property-read TableRecord|null $defaultValuesRecord
 * @property-read TablePermissions[] $permissions
 * @property-read TableField[] $usedByFields
 * @property-read Table[] $usedByTables
 * @property-read null|bool|string|int $recordsCount
 */
class Table extends BaseModel
{
    use HasModulePropertiesTrait;

    public const GUEST_USER_ID = -1;

    /**
     * @var array
     */
    private $_fieldsIdsByNames = null;

    /**
     * @var array
     */
    private $_cachedPermissions = null;

    /**
     * @var array
     */
    private $_cachedUsersPermissions = [];

    /**
     * Тип таблица - Главная
     */
    public const TABLE_TYPE_MAIN = 1;

    /**
     * Тип таблица - Вспомогательная
     */
    public const TABLE_TYPE_AUXILIARY = 2;

    public function __construct($config = [])
    {
        $this->widget_class_viewer = RecordsViewerWidget::class;
        $this->widget_class_toolbar = RecordsViewerToolbarWidget::class;
        $this->widget_class_search = RecordsSearchWiget::class;

        $this->table_behaviors = [];
        $this->records_behaviors = [];
        $this->records_assets = [];

        parent::__construct($config);
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%registry_tables}}';
    }

    /**
     * @return TableQuery|ActiveQuery
     * @noinspection PhpUnhandledExceptionInspection
     * @noinspection PhpDocMissingThrowsInspection
     */
    public static function find()
    {
        return Yii::createObject(TableQuery::class, [get_called_class()]);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['widget_class_viewer', 'default', 'value' => RecordsViewerWidget::class],
            ['widget_class_toolbar', 'default', 'value' => RecordsViewerToolbarWidget::class],
            ['widget_class_search', 'default', 'value' => RecordsSearchWiget::class],

            [
                [
                    'table_behaviors',
                    'records_behaviors',
                    'records_assets',
                ],
                'safe',
            ],

            [
                [
                    'table_behaviors',
                    'records_behaviors',
                    'records_assets',
                ],
                'default',
                'value' => [],
            ],

            ['table_behaviors', 'validateTableBehaviors', 'skipOnEmpty' => false, 'skipOnError' => false],
            ['records_behaviors', 'validateRecordsBehaviors', 'skipOnEmpty' => false, 'skipOnError' => false],
            ['records_assets', 'validateRecordsAssets', 'skipOnEmpty' => false, 'skipOnError' => false],

            [
                [
                    'name',
                    'type',
                    'widget_class_viewer',
                    'widget_class_toolbar',
                    'widget_class_search',
                ],
                'required',
                'except' => [static::SCENARIO_SEARCH],
            ],

            [
                [
                    'widget_class_viewer',
                    'widget_class_toolbar',
                    'widget_class_search',
                ],
                'string',
                'max' => 255,
            ],

            ['default_page_size', 'default', 'value' => 20],
            ['default_page_size', 'integer', 'min' => 1, 'max' => 200],
            ['visible_in_menu', 'default', 'value' => false],
            ['visible_in_menu', 'boolean'],
            ['type', 'in', 'range' => array_keys(static::getTypeHeaders())],
            [['name', 'inline_markup'], 'string', 'max' => 255],
            ['token', 'string', 'length' => 32, 'when' => function () {
                return $this->getIsNewRecord();
            }],
        ];
    }

    /**
     * @noinspection PhpUnused
     */
    public function validateTableBehaviors() {
        if (!is_array($this->table_behaviors)) {
            $this->addError('table_behaviors', 'Не является массивом');
        }

        if (empty($this->table_behaviors)) {
            return;
        }

        foreach ($this->table_behaviors as $behavior) {
            if (!is_string($behavior) || !is_subclass_of($behavior, Behavior::class)) {
                $this->addError('table_behaviors', 'Класс не является потомком Behavior');
            }
        }
    }

    /**
     * @noinspection PhpUnused
     */
    public function validateRecordsBehaviors() {
        if (!is_array($this->records_behaviors)) {
            $this->addError('records_behaviors', 'Не является массивом');
        }

        if (empty($this->records_behaviors)) {
            return;
        }

        foreach ($this->records_behaviors as $behavior) {
            if (!is_string($behavior) || !is_subclass_of($behavior, Behavior::class)) {
                $this->addError('records_behaviors', 'Класс не является потомком Behavior');
            }
        }
    }

    /**
     * @noinspection PhpUnused
     */
    public function validateRecordsAssets() {
        if (!is_array($this->records_assets)) {
            $this->addError('records_assets', 'Не является массивом');
        }

        if (empty($this->records_assets)) {
            return;
        }

        foreach ($this->records_assets as $assetClass) {
            if (!is_string($assetClass) || !is_subclass_of($assetClass, AssetBundle::class)) {
                $this->addError('table_behaviors', 'Класс не является потомком Behavior');
            }
        }
    }

    public function afterFind()
    {
        parent::afterFind();

        $this->table_behaviors = json_decode($this->table_behaviors);
        $this->records_behaviors = json_decode($this->records_behaviors);
        $this->records_assets = json_decode($this->records_assets);

        foreach ($this->table_behaviors as $class) {
            $this->attachBehavior(0, ['class' => $class]);
        }
    }

    /**
     * @return array|array[]
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[static::SCENARIO_DEFAULT][] = '!token';

        return $scenarios;
    }

    public static function getTypeHeaders()
    {
        return [
            static::TABLE_TYPE_MAIN => 'Главная',
            static::TABLE_TYPE_AUXILIARY => 'Вспомогательная',
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
            'name' => 'Название таблицы',
            'token' => 'Токен таблицы',
            'inline_markup' => 'Шаблон отображаемого названия',
            'type' => 'Тип таблицы',
            'visible_in_menu' => 'Отображать таблицу в меню?',
            'default_page_size' => 'Количество выводимых записей на странице',
            'widget_class_viewer' => 'Viewer widget class',
            'widget_class_toolbar' => 'Toolbar widget class',
            'widget_class_search' => 'Search widget class',
            'table_behaviors' => 'Table model behaviors',
            'records_behaviors' => 'Table record model behaviors',
            'records_assets' => 'Table record form assets',
        ];
    }

    /**
     * @return TableFieldQuery
     * @noinspection PhpUnused
     * @noinspection PhpIncompatibleReturnTypeInspection
     * @noinspection PhpReturnDocTypeMismatchInspection
     */
    public function getFields()
    {
        return $this
            ->hasMany(TableField::class, ['registry_table_id' => 'id'])
            ->orderBy(['sort' => SORT_ASC])
            ->indexBy('id');
    }

    /**
     * @return TablePermissionsQuery
     * @noinspection PhpUnused
     * @noinspection PhpIncompatibleReturnTypeInspection
     * @noinspection PhpReturnDocTypeMismatchInspection
     */
    public function getPermissions()
    {
        return $this
            ->hasMany(TablePermissions::class, ['registry_table_id' => 'id'])
            ->indexBy(function ($permissions) {
                /** @var TablePermissions $permissions */
                return $permissions->mainRole;
            });
    }

    /**
     * @return TableFieldQuery
     * @noinspection PhpUnused
     * @noinspection PhpIncompatibleReturnTypeInspection
     * @noinspection PhpReturnDocTypeMismatchInspection
     */
    public function getUsedByFields()
    {
        return $this
            ->hasMany(TableField::class, ['registry_variants_table_id' => 'id'])
            ->orderBy(['sort' => SORT_ASC])
            ->indexBy('id');
    }

    /**
     * @return TableQuery
     * @noinspection PhpUnused
     */
    public function getUsedByTables()
    {
        $queryFields = $this->getUsedByFields();
        $queryFields->select(['{{%registry_table_fields}}.registry_table_id']);
        $queryFields->groupBy(['{{%registry_table_fields}}.registry_table_id']);

        return Table::find()
            ->innerJoin(['f' => $queryFields], '{{%registry_tables}}.id = f.registry_table_id');
    }

    /**
     * @param int|string $fieldId
     * @param null $default
     * @param bool $populate
     *
     * @return TableField|null
     * @noinspection PhpMissingParamTypeInspection
     */
    public function getField($fieldId, $default = null, $populate = false)
    {
        if ($this->isRelationPopulated('fields') || $populate) {
            if ($fieldId && isset($this->fields[$fieldId])) {
                return $this->fields[$fieldId];
            }
        }

        return ($default instanceof Closure)
            ? $default()
            : $default;
    }

    /**
     * @param string $name
     * @param null $default
     * @param bool $populate
     *
     * @return TableField|null
     * @noinspection PhpMissingParamTypeInspection
     */
    public function getFieldByName(string $name, $default = null, $populate = false)
    {
        return $this->getField($this->getFieldIdByName($name), $default, $populate);
    }

    /**
     * @param string $name
     *
     * @return false|int
     */
    public function getFieldIdByName(string $name)
    {
        if ($this->_fieldsIdsByNames === null) {
            foreach ($this->fields as $field) {
                $this->_fieldsIdsByNames[$field->name] = $field->id;
            }
        }

        if (isset($this->_fieldsIdsByNames[$name])) {
            return $this->_fieldsIdsByNames[$name];
        }

        return false;
    }

    /**
     * @return TableRecordQuery
     * @noinspection PhpIncompatibleReturnTypeInspection
     * @noinspection PhpReturnDocTypeMismatchInspection
     * @noinspection RedundantSuppression
     */
    public function getRecords()
    {
        return $this
            ->hasMany(TableRecord::class, ['registry_table_id' => 'id'])
            ->andWhere(['is_system' => false])
            ->andWhereHasViewPermissions();
    }

    /**
     * @return bool|int|string|null
     */
    public function getRecordsCount()
    {
        return $this->getRecords()->count();
    }

    /**
     * @return TableRecordQuery
     * @noinspection PhpIncompatibleReturnTypeInspection
     * @noinspection PhpReturnDocTypeMismatchInspection
     * @noinspection PhpUnused
     */
    protected function getDefaultValuesRecord()
    {
        return $this
            ->hasOne(TableRecord::class, ['registry_table_id' => 'id'])
            ->andWhere(['is_system' => true]);
    }

    /**
     * @param int|string|null $userId User id, can be null
     * @param string $permissionName You can specify additional permissions via seconds argument, third argument etc.
     *
     * @throws ForbiddenHttpException
     */
    public function throwIfNoAccess($userId, string $permissionName)
    {
        $hasAccess = call_user_func_array([$this, 'checkAccess'], func_get_args());

        if (!$hasAccess) {
            throw new ForbiddenHttpException();
        }
    }

    /**
     * @param int|string|null $userId
     *
     * @return bool
     */
    public function canAddRecords($userId = null)
    {
        return $this->checkAccess($userId, TablePermissions::CAN_ADD_RECORDS);
    }

    /**
     * @param int|string|null $userId User id, can be null
     * @param string $permissionName Permission name
     *
     * @return bool
     */
    public function checkAccess($userId, string $permissionName)
    {
        if ($userId === null) {
            $userId = Yii::$app->user->id;
        }

        if ($userId === null) {
            $userId = static::GUEST_USER_ID;
        } else {
            $userId = (int) $userId;
        }

        if ($userId === static::GUEST_USER_ID) {
            return $this->checkPermissionAccess(TablePermissions::SPECIAL_ROLE_GUEST, $permissionName);
        }

        if ($this->checkPermissionAccess(TablePermissions::SPECIAL_ROLE_AUTH, $permissionName)) {
            return true;
        }

        $this->loadUserPermissions($userId);

        return $this->_cachedUsersPermissions[$userId][$permissionName] ?? false;
    }

    /**
     * @param string $role
     * @param string $permissionName
     *
     * @return bool
     */
    public function checkPermissionAccess(string $role, string $permissionName)
    {
        $this->loadPermissionsFromCache();

        return isset($this->_cachedPermissions[$role])
            && isset($this->_cachedPermissions[$role][$permissionName]);
    }

    /**
     * @param string|int $userId
     *
     * @return void
     */
    protected function loadUserPermissions($userId)
    {
        if (isset($this->_cachedUsersPermissions[$userId])) {
            return;
        }

        // cache user permissions
        $userRoles = array_keys(Yii::$app->authManager->getRolesByUser($userId));

        foreach (TablePermissions::getPermissionsList() as $permissionName) {
            foreach ($userRoles as $userRole) {
                if ($this->checkPermissionAccess($userRole, $permissionName)) {
                    $this->_cachedUsersPermissions[$userId][$permissionName] = true;
                    break;
                }
            }

            $this->_cachedUsersPermissions[$userId][$permissionName] =
                $this->_cachedUsersPermissions[$userId][$permissionName] ?? false;
        }
    }

    /**
     * @return void
     */
    protected function loadPermissionsFromCache()
    {
        if ($this->_cachedPermissions !== null) {
            return;
        }

        $this->_cachedPermissions = $this->module->cache->getOrSet($this->getCacheKey('permissions'), function () {
            $result = [];

            foreach ($this->permissions as $permission) {
                foreach (TablePermissions::getPermissionsList() as $permissionName) {
                    $result[$permission->mainRole][$permissionName] =
                        $permission->isAllowed($permissionName);
                }
            }

            return $result;
        }, 0);
    }

    public function invalidateCache()
    {
        $this->_cachedPermissions = null;
        $this->module->cache->delete($this->getCacheKey('permissions'));
    }

    /**
     * @param string|int $tableId
     * @param string|null $key
     *
     * @return string
     */
    public static function generateCacheKey($tableId, string $key = null)
    {
        return $key === null
            ? "reg:con:table:{$tableId}"
            : "reg:con:table:{$tableId}:$key";
    }

    /**
     * @param string|null $key
     *
     * @return string
     */
    public function getCacheKey(string $key = null)
    {
        return static::generateCacheKey($this->id, $key);
    }

    /**
     * @param boolean $insert
     *
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        if ($insert) {
            do {
                $this->token = static::generateToken();
                $tokenExist = static::find()->andWhereToken($this->token)->exists();
            } while($tokenExist);
        }

        $this->table_behaviors = json_encode(array_values($this->table_behaviors));
        $this->records_behaviors = json_encode(array_values($this->records_behaviors));
        $this->records_assets = json_encode(array_values($this->records_assets));

        return true;
    }

    /**
     * @return string
     */
    public static function generateToken()
    {
        return uniqid('t');
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        $this->table_behaviors = json_decode($this->table_behaviors);
        $this->records_behaviors = json_decode($this->records_behaviors);
        $this->records_assets = json_decode($this->records_assets);
    }

    /**
     * @return bool
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function beforeDelete()
    {
        if (!parent::beforeDelete()) {
            return false;
        }

        foreach ($this->records as $record) {
            $record->delete();
        }

        return true;
    }

    public function load($data, $formName = null)
    {
        if (!parent::load($data, $formName)) {
            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function getErrorSummary($showAllErrors)
    {
        $lines = [];
        $errors = $showAllErrors ? $this->getErrors() : $this->getFirstErrors();

        foreach ($errors as $attributeName => $es) {
            $attr = $this->getAttributeLabel($attributeName);
            $lines = array_merge($lines, (array) "{$attr} - {$es}");
        }

        return $lines;
    }
}
