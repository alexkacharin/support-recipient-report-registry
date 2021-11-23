<?php

namespace Matodor\RegistryConstructor\models;

use Closure;
use common\models\User;
use Matodor\Common\models\BaseModel;
use Matodor\RegistryConstructor\components\ValueType;
use Matodor\RegistryConstructor\models\forms\TableRecordForm;
use Matodor\RegistryConstructor\queries\TableQuery;
use Matodor\RegistryConstructor\queries\TableRecordQuery;
use Matodor\RegistryConstructor\queries\TableRecordValueQuery;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\db\ActiveQueryInterface;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * @property int $id
 * @property int $registry_table_id
 * @property int|null $user_created_id
 * @property int|null $user_updated_id
 * @property boolean $is_system
 *
 * @property-read Table $table
 * @property-read string $updatedByUserTitle
 * @property-read string $createdByUserTitle
 * @property-read TableRecordValue[] $values
 * @property-read User|null $createdUser
 * @property-read User|null $updatedUser
 */
class TableRecord extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%registry_table_records}}';
    }

    /**
     * @return TableRecordQuery|ActiveQuery
     * @noinspection PhpUnhandledExceptionInspection
     * @noinspection PhpDocMissingThrowsInspection
     */
    public static function find()
    {
        return Yii::createObject(TableRecordQuery::class, [get_called_class()]);
    }

    public function afterFind()
    {
        parent::afterFind();

        if (!$this->is_system) {
            foreach ($this->table->records_behaviors as $class) {
                $this->attachBehavior(0, ['class' => $class]);
            }
        }
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[static::SCENARIO_DEFAULT][] = '!registry_table_id';
        $scenarios[static::SCENARIO_DEFAULT][] = '!is_system';

        return $scenarios;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['registry_table_id'], 'required', 'except' => [static::SCENARIO_SEARCH]],
            [['registry_table_id'], 'integer'],
            [['registry_table_id'], 'exist', 'except' => [static::SCENARIO_SEARCH], 'targetClass' => Table::class, 'targetAttribute' => 'id'],

            [['user_created_id', 'user_updated_id'], 'default', 'value' => null],
            [['user_created_id', 'user_updated_id'], 'exist', 'targetClass' => User::class, 'targetAttribute' => 'id'],

            ['is_system', 'boolean'],
            ['is_system', 'default', 'value' => false],
        ];
    }

    /**
     * {@inheritdoc}
     * @noinspection DuplicatedCode
     * @noinspection TranslationsCorrectnessInspection
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('registryConstructor', 'Идентификатор'),
            'registry_table_id' => Yii::t('registryConstructor', 'ID реестра'),

            'createdUser' => Yii::t('registryConstructor', 'ID пользователя создавшего запись'),
            'user_created_id' => Yii::t('registryConstructor', 'ID пользователя создавшего запись'),
            'createdByUserTitle' => Yii::t('registryConstructor', 'Пользователь создавший запись'),

            'updatedUser' => Yii::t('registryConstructor', 'ID пользователя обновившего запись'),
            'user_updated_id' => Yii::t('registryConstructor', 'ID пользователя обновившего запись'),
            'updatedByUserTitle' => Yii::t('registryConstructor', 'Пользователь обновивший запись'),
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

    /**
     * @return ActiveQueryInterface
     * @noinspection PhpUnused
     */
    public function getCreatedUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_created_id']);
    }

    /**
     * @return ActiveQueryInterface
     * @noinspection PhpUnused
     */
    public function getUpdatedUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_updated_id']);
    }

    /**
     * @param int|string $fieldId
     * @param Closure|null|mixed $default
     *
     * @return TableRecordValue|null
     * @noinspection PhpIncompatibleReturnTypeInspection
     * @noinspection PhpParameterNameChangedDuringInheritanceInspection
     */
    public function getValue($fieldId, $default = null)
    {
        if ($fieldId && isset($this->values[$fieldId])) {
            return $this->values[$fieldId];
        }

        return ($default instanceof Closure)
            ? $default()
            : $default;
    }

    /**
     * @param TableField $field
     *
     * @return Closure|TableRecordValue|null
     * @throws InvalidConfigException
     */
    public function getValueOrInstantiate(TableField $field)
    {
        return $this->getValue($field->id, function () use ($field) {
            return $field->instantiateRecordValue();
        });
    }

    /**
     * @param string $fieldName
     * @param Closure|null|mixed $default
     *
     * @return TableRecordValue|null
     */
    public function getValueByFieldName(string $fieldName, $default = null)
    {
        return $this->getValue($this->table->getFieldIdByName($fieldName), $default);
    }

    /**
     * @return string
     */
    public function getUpdatedByUserTitle()
    {
        if ($this->user_updated_id !== null) {
            return $this->updatedUser->username;
        } else {
            return '-';
        }
    }

    /**
     * @return string
     */
    public function getCreatedByUserTitle()
    {
        if ($this->user_created_id !== null) {
            return $this->createdUser->username;
        } else {
            return '-';
        }
    }

    /**
     * @param string $class
     * @param array $selects
     *
     * @return ActiveQuery
     */
    protected function getSubQueryValue($class, $selects)
    {
        return $this
            ->hasMany($class, ['registry_table_record_id' => 'id'])
            ->select($selects);
    }

    /**
     * @return TableRecordValueQuery
     */
    public function getValues()
    {
        // TODO make cacheable $subSelects
        // TODO get value classes only for record table fields

        $valueClasses = ValueType::instance()->getRecordValueClasses();
        $maxAddColumns = 0;
        $subSelects = [];

        /** @var ActiveQuery $subQuery */
        $subQuery = null;

        foreach ($valueClasses as $class) {
            if ($maxAddColumns < count($class::additionalColumns())) {
                $maxAddColumns = count($class::additionalColumns());
            }
        }

        foreach ($valueClasses as $class) {
            $subSelects[$class] = array_map(function ($col) use ($class) {
                return $class::tableName() . '.' . $col;
            }, $class::commonColumns());

            $cols = array_values($class::additionalColumns());
            for ($i = 0; $i < $maxAddColumns; $i++) {
                if (isset($cols[$i])) {
                    $subSelects[$class][] = $class::tableName() . '.' . $cols[$i] . " as col_{$i}";
                } else {
                    $subSelects[$class][] = new Expression("NULL AS `col_{$i}`");
                }
            }

            if ($subQuery === null) {
                $subQuery = $this->getSubQueryValue($class, $subSelects[$class]);
            } else {
                $subQuery->union($this->getSubQueryValue($class, $subSelects[$class]), true);
            }
        }

        $query = new TableRecordValueQuery(ActiveRecord::class);
        $query->select('*');
        $query->from(['u' => $subQuery]);
        $query->indexBy(['registry_table_field_id']);
        $query->multiple = true;
        $query->primaryModel = $this;
        $query->link = ['registry_table_record_id' => 'id'];

        if ($this instanceof TableRecordForm) {
            $query->asForm = true;
        }

        return $query;
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        if ($insert) {
            $this->user_created_id = Yii::$app->user->id;
        }

        $this->user_updated_id = Yii::$app->user->id;
        return true;
    }

    public function beforeDelete()
    {
        if (!parent::beforeDelete()) {
            return false;
        }

        foreach ($this->values as $recordValue) {
            $recordValue->delete();
        }

        return true;
    }

    /**
     * @param int|string|null $userId
     *
     * @return bool
     */
    public function canDelete($userId = null)
    {
        return $this->table->checkAccess($userId, TablePermissions::CAN_DELETE_ALL_RECORDS)
            || $this->user_created_id === $userId
            && $this->table->checkAccess($userId, TablePermissions::CAN_DELETE_SELF_RECORDS);
    }

    /**
     * @param int|string|null $userId
     *
     * @return bool
     */
    public function canUpdate($userId = null)
    {
        return $this->table->checkAccess($userId, TablePermissions::CAN_EDIT_ALL_RECORDS)
            || $this->user_created_id === $userId
            && $this->table->checkAccess($userId, TablePermissions::CAN_EDIT_SELF_RECORDS);
    }

    /**
     * @param int|string|null $userId
     *
     * @return bool
     */
    public function canView($userId = null)
    {
        return $this->table->checkAccess($userId, TablePermissions::CAN_VIEW_ALL_RECORDS)
            || $this->user_created_id === $userId
            && $this->table->checkAccess($userId, TablePermissions::CAN_VIEW_SELF_RECORDS);
    }
}
