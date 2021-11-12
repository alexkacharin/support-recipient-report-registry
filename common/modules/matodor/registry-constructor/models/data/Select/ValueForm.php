<?php

/** @noinspection PhpHierarchyChecksInspection */

namespace Matodor\RegistryConstructor\models\data\Select;

use Matodor\RegistryConstructor\models\forms\TableRecordForm;
use Matodor\RegistryConstructor\models\TableRecord;
use Matodor\RegistryConstructor\traits\TableRecordValueFormTrait;
use yii\helpers\ArrayHelper;

/**
 * @property-read array $variantsSelectData
 *
 * @method Settings getFieldSettings()
 *
 * @mixin TableRecordValueFormTrait
 */
class ValueForm extends Value
{
    use TableRecordValueFormTrait;

    /**
     * @var TableRecordForm|null
     */
    public $newVariantRecord = null;

    public function attributeLabels()
    {
        $labels = parent::attributeLabels();
        $labels['newVariantRecord'] = "Новое значение для поля \"{$this->field->name}\"";

        return $labels;
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[static::SCENARIO_DEFAULT][] = '!newVariantRecord';

        return $scenarios;
    }

    public function initNewVariantRecord()
    {
        if ($this->newVariantRecord !== null ||
            !$this->field->variantsTable->canAddRecords()
        ) {
            return;
        }

        $this->newVariantRecord = new TableRecordForm();
        $this->newVariantRecord->registry_table_id = $this->field->variantsTable->id;
        $this->newVariantRecord->populateRelation('table', $this->field->variantsTable);
        $this->newVariantRecord->parentModel = $this;
        $this->newVariantRecord->parentModelAttribute = 'new';

        foreach ($this->field->variantsTable->fields as $field) {
            $this->newVariantRecord->editValues[$field->id]
                = $this->newVariantRecord->values[$field->id] ?? $field->instantiateRecordValue(true);
            $this->newVariantRecord->editValues[$field->id]->parentModel = $this->newVariantRecord;
            $this->newVariantRecord->editValues[$field->id]->parentModelAttribute = 'editValues';
        }
    }

    /**
     * @return array
     * @noinspection PhpUnused
     */
    public function getVariantsSelectData()
    {
        if (!$this->field->getHasVariantsTable()) {
            return [];
        }

        // TODO: при реализации кеширования необходимо учитывать доступность
        // variantsTable с учетом прав доступа пользователя, а так же всех дочерних таблиц variantsTable и т.д

        return ArrayHelper::map($this->field->variantsTable->records, 'id', function ($record) {
            /** @var TableRecord $record */
            return $this->getFieldSettings()->getFormattedValue($record);
        });
    }

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function load($data, $formName = null)
    {
        if (!parent::load($data, $formName)) {
            return false;
        }

        $scope = $formName === null ? $this->formName() : $formName;
        $data = $scope !== '' ? ArrayHelper::getValue($data, $scope) : $data;
        $dataVariant = ArrayHelper::getValue($data, 'new');

        if (is_array($dataVariant)) {
            $this->initNewVariantRecord();

            if (!$this->newVariantRecord->load($dataVariant, '')) {
                return false;
            }
        }

        return true;
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        if ($this->newVariantRecord !== null) {
            if ($this->newVariantRecord->save()) {
                $this->value_record_id = $this->newVariantRecord->id;
            } else {
                $this->addError('newVariantRecord',
                    "Ошибка при сохранении нового значения для \"{$this->field->name}\"");
            }
        }

        return true;
    }
}
