<?php

namespace Matodor\RegistryConstructor\models\forms;

use Matodor\Common\behaviors\MultipleRelationSaverBehavior;
use Matodor\Common\traits\HasParentFormNameTrait;
use Matodor\RegistryConstructor\models\TableRecord;
use Matodor\RegistryConstructor\models\TableRecordValue;
use Matodor\RegistryConstructor\traits\TableRecordValueFormTrait;
use yii\helpers\ArrayHelper;

/**
 * @mixin MultipleRelationSaverBehavior
 */
class TableRecordForm extends TableRecord
{
    use HasParentFormNameTrait;

    /**
     * @var array|TableRecordValue[]|TableRecordValueFormTrait[]
     */
    public $editValues = [];

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
                    'attribute' => 'editValues',
                    'class' => TableRecordValue::class,
                    'foreignKey' => 'registry_table_record_id',
                    'deleteNotProvided' => false,
                    'modelsFactory' => function ($data, $parent) {
                       return $this->editValues;
                    },
                ],
            ],
        ];

        return $behaviors;
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[static::SCENARIO_DEFAULT][] = '!registry_table_id';
        $scenarios[static::SCENARIO_DEFAULT][] = '!editValues';

        return $scenarios;
    }

    public function load($data, $formName = null)
    {
        if (!parent::load($data, $formName)) {
            return false;
        }

        $scope = $formName === null ? $this->formName() : $formName;
        $data = $scope !== '' ? ArrayHelper::getValue($data, $scope) : $data;
        $data = ArrayHelper::getValue($data, 'editValues');

        if ($data !== null) {
            foreach ($data as $fieldValue) {
                $fieldId = ArrayHelper::getValue($fieldValue, 'registry_table_field_id');

                if ($fieldId !== null
                    && isset($this->editValues[$fieldId])
                ) {
                    $this
                        ->editValues[$fieldId]
                        ->load($fieldValue, '');
                }
            }
        }

        return true;
    }

    public function rules()
    {
        $rules = parent::rules();
        $rules[] = ['editValues', 'safe'];

        return $rules;
    }

    public function attributeLabels()
    {
        $labels = parent::attributeLabels();
        $labels['editValues'] = 'Поля записи';

        return $labels;
    }

    public function formName()
    {
        if ($this->parentModel === null) {
            return 'TableRecordForm';
        } else {
            return $this->parentFormName();
        }
    }
}
