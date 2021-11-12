<?php

namespace Matodor\RegistryConstructor\models\forms;

use Matodor\Common\behaviors\MultipleRelationSaverBehavior;
use Matodor\Common\components\Helper;
use Matodor\RegistryConstructor\models\data\ChildRecord\Value;
use Matodor\RegistryConstructor\models\Table;
use Yii;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;

class TableForm extends Table
{
    /**
     * @var array|TableFieldForm[]
     */
    public $editFields = [];

    /**
     * @var array|TablePermissionsForm[]
     */
    public $editPermissions = [];

    /**
     * @var TableRecordForm
     */
    public $editDefaultValuesRecord = null;

    /**
     * @throws InvalidConfigException
     */
    public function afterFind()
    {
        parent::afterFind();

        // setup fields
        {
            $query = $this->getFields();
            $query->modelClass = TableFieldForm::class;
            $fields = $query->all();
            $this->populateRelationIfNeeded('fields', $fields);

            if (Yii::$app->request->isGet) {
                $this->editFields = $fields;
            }
        }

        // setup permissions
        {
            $query = $this->getPermissions();
            $query->modelClass = TablePermissionsForm::class;
            $permissions = $query->all();
            $this->populateRelationIfNeeded('permissions', $permissions);

            if (Yii::$app->request->isGet) {
                $this->editPermissions = $permissions;
            }
        }

        // setup default values record
        {
            $query = $this->getDefaultValuesRecord();
            $query->modelClass = TableRecordForm::class;
            $this->editDefaultValuesRecord = $query->one();
            $this->populateRelationIfNeeded('defaultValuesRecord', $this->editDefaultValuesRecord);

            if ($this->editDefaultValuesRecord !== null) {
                $this->editDefaultValuesRecord->parentModel = $this;
                $this->editDefaultValuesRecord->parentModelAttribute = 'editDefaultValuesRecord';
                $this->editDefaultValuesRecord->populateRelation('table', $this);

                foreach ($this->fields as $field) {
                    $fieldValue = $this->editDefaultValuesRecord->values[$field->id]
                        ?? $field->instantiateRecordValue(true);
                    $fieldValue->parentModel = $this->editDefaultValuesRecord;
                    $fieldValue->parentModelAttribute = 'editValues';

                    $this->editDefaultValuesRecord->editValues[$field->id] = $fieldValue;
                }
            }
        }
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[static::SCENARIO_DEFAULT][] = '!editDefaultValuesRecord';

        return $scenarios;
    }

    /**
     * @noinspection MissedFieldInspection
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors[] = [
            'class' => MultipleRelationSaverBehavior::class,
            'processFields' => [
                [
                    'attribute' => 'editFields',
                    'class' => TableFieldForm::class,
                    'foreignKey' => 'registry_table_id',
                    'modelFactory' => function ($itemData, $itemId, $index, $key, $parent) {
                        /** @var TableForm $parent */
                            return !Helper::isEmpty($itemId)
                                && !$parent->isNewRecord
                                    ? $parent->getField($itemId, null, true)
                                    : null;
                    },
                    'beforeSave' => function ($model, $index) {
                        /** @var TableFieldForm $model */
                        $model->sort = $index;
                    },
                ],
                [
                    'attribute' => 'editPermissions',
                    'class' => TablePermissionsForm::class,
                    'foreignKey' => 'registry_table_id',
                ],
            ],
        ];

        return $behaviors;
    }

    public function attributeLabels()
    {
        $labels = parent::attributeLabels();
        $labels['editFields'] = 'Поля таблицы';
        $labels['editPermissions'] = 'Разрешения таблицы';
        $labels['editDefaultValuesRecord'] = 'Значения полей по умолчанию';

        return $labels;
    }

    public function rules()
    {
        $rules = parent::rules();
        $rules[] = ['editFields', 'safe'];
        $rules[] = ['editFields', 'validateEditFields', 'skipOnEmpty' => false];
        $rules[] = ['editPermissions', 'safe'];
        $rules[] = ['editPermissions', 'validateEditPermissions', 'skipOnEmpty' => false];
        $rules[] = ['editDefaultValuesRecord', 'validateEditDefaultValuesRecord', 'skipOnEmpty' => false];

        return $rules;
    }

    /**
     * @return void
     * @noinspection PhpUnused
     */
    public function validateEditDefaultValuesRecord()
    {
        if ($this->editDefaultValuesRecord !== null
            && !$this->editDefaultValuesRecord->validate()
        ) {
            $this->addError('editDefaultValuesRecord', 'Ошибки при заполнении значений по умолчанию');
        }
    }

    /**
     * @return void
     * @noinspection PhpUnused
     */
    public function validateEditPermissions()
    {
        $valid = true;

        if (!empty($this->editPermissions)) {
            foreach ($this->editPermissions as $key1 => $permission1) {
                foreach ($this->editPermissions as $key2 => $permission2) {
                    if ($key1 === $key2
                        || $permission2->hasErrors('role')
                    ) {
                        continue;
                    }

                    if (!Helper::isEmpty($permission1->role)
                        && !Helper::isEmpty($permission2->role)
                        && $permission1->role === $permission2->role
                    ) {
                        if (!$permission1->hasErrors('role')) {
                            $permission1->addError('role', 'Удалите повторяющиеся роли');
                        }

                        $permission2->addError('role', 'Удалите повторяющиеся роли');
                        $valid = false;
                    }
                }
            }
        }

        if (!$valid) {
            $this->addError('editPermissions', 'Удалите повторяющиеся роли');
        }
    }

    /**
     * @return void
     * @noinspection PhpUnused
     */
    public function validateEditFields()
    {
        if (empty($this->editFields)) {
            $this->addError('editFields', 'Таблица не может быть пустой!');
        }
    }

    public function formName()
    {
        return 'TableForm';
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if ($insert || $this->editDefaultValuesRecord === null) {
            $this->editDefaultValuesRecord = new TableRecordForm();
            $this->editDefaultValuesRecord->registry_table_id = $this->id;
            $this->editDefaultValuesRecord->is_system = true;
            $this->editDefaultValuesRecord->populateRelation('table', $this);

            if (!$this->editDefaultValuesRecord->save()) {
                throw new Exception('Error while saving `editDefaultValuesRecord`');
            }
        } else {
            $this->editDefaultValuesRecord->save();
        }
    }

    public function load($data, $formName = null)
    {
        if (!parent::load($data, $formName)) {
            return false;
        }

        $scope = $formName === null ? $this->formName() : $formName;
        $data = $scope !== '' ? ArrayHelper::getValue($data, $scope) : $data;
        $dataDefaultValues = ArrayHelper::getValue($data, 'editDefaultValuesRecord');

        if (is_array($dataDefaultValues)) {
            if (!$this->editDefaultValuesRecord->load($dataDefaultValues, '')) {
                return false;
            }
        }

        return true;
    }
}
