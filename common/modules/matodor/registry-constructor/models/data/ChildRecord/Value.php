<?php

namespace Matodor\RegistryConstructor\models\data\ChildRecord;

use Matodor\RegistryConstructor\models\data\Select\Value as SelectValue;
use Matodor\RegistryConstructor\models\TableRecord;

/**
 * @property integer|null $value_record_id
 *
 * @property-read TableRecord|null $valueRecord
 *
 * @method Settings getFieldSettings()
 */
class Value extends SelectValue
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = parent::rules();
        $rules[] = ['valueRecord', 'validateValueRecord', 'skipOnEmpty' => false];

        return $rules;
    }

    public function getIsRequiredValue()
    {
        return false;
    }

    protected function validateValueRecord()
    {
        if (!$this->valueRecord->validate()) {
            $this->addError('valueRecord', 'Исправьте ошибки при заполнении записи');
        }
    }
}
