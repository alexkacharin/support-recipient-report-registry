<?php

namespace Matodor\RegistryConstructor\models\data\Email;

use Matodor\RegistryConstructor\models\data\String\Value as StringValue;

/**
 * @property string $value_string
 *
 * @method Settings getFieldSettings()
 */
class Value extends StringValue
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = parent::rules();
        $rules['valueStringEmail'] = ['value_string', 'email'];

        return $rules;
    }

    /**
     * @inheritDoc
     */
    public static function getSelectTitle()
    {
        return 'E-mail';
    }

    public function loadFromImport($value)
    {
        $this->value_string = (string) $value;
    }
}
