<?php

/** @noinspection PhpHierarchyChecksInspection */

namespace Matodor\RegistryConstructor\models\data\ChildRecord;

use Matodor\RegistryConstructor\models\forms\TableRecordForm;
use Matodor\RegistryConstructor\traits\TableRecordValueFormTrait;
use yii\base\InvalidConfigException;
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

    public function init()
    {
        parent::init();

        if ($this->getIsNewRecord()) {
            $this->populateRelation('valueRecord', new TableRecordForm());
            $this->setupRecord();
        }
    }

    public function afterFind()
    {
        parent::afterFind();

        // setup record
        {
            $query = $this->getValueRecord();
            $query->modelClass = TableRecordForm::class;
            $this->populateRelation('valueRecord', $query->one());
            $this->setupRecord();
        }
    }

    protected function setupRecord()
    {
        if ($this->valueRecord === null
            || !$this->field->getHasVariantsTable()
            || !$this->field->variantsTable->canAddRecords()
        ) {
            return;
        }

        /** @var TableRecordForm $record */
        $record = $this->valueRecord;
        $record->registry_table_id = $this->field->registry_variants_table_id;
        $record->populateRelation('table', $this->field->variantsTable);
        $record->parentModel = $this;
        $record->parentModelAttribute = 'record';

        foreach ($this->field->variantsTable->fields as $field) {
            if ($record->getIsNewRecord()) {
                $record->editValues[$field->id] = $field->instantiateRecordValue(true);
            } else {
                $record->editValues[$field->id] =
                    $record->values[$field->id] ?? $field->instantiateRecordValue(true);
            }

            $record->editValues[$field->id]->parentModel = $record;
            $record->editValues[$field->id]->parentModelAttribute = 'editValues';
        }
    }

    /**
     * @throws InvalidConfigException
     */
    public function load($data, $formName = null)
    {
        if (!parent::load($data, $formName)) {
            return false;
        }

        $scope = $formName === null ? $this->formName() : $formName;
        $data = $scope !== '' ? ArrayHelper::getValue($data, $scope) : $data;
        $dataRecord = ArrayHelper::getValue($data, 'record');

        if (is_array($dataRecord)) {
            if (!$this->valueRecord->load($dataRecord, '')) {
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

        if ($this->valueRecord !== null) {
            if ($this->valueRecord->save()) {
                $this->value_record_id = $this->valueRecord->id;
            } else {
                $this->addError('childRecord',
                    "Ошибка при сохранении нового значения для \"{$this->field->name}\"");
            }
        }

        return true;
    }
}
