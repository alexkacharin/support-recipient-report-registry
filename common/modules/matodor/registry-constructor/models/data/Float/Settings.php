<?php

/** @noinspection PhpHierarchyChecksInspection */

namespace Matodor\RegistryConstructor\models\data\Float;

use Matodor\RegistryConstructor\models\data\HasPostfixPrefixSettings;
use Matodor\RegistryConstructor\models\data\HasTemplatesSettings;
use Matodor\RegistryConstructor\models\forms\TableFieldSettingsForm;

class Settings extends TableFieldSettingsForm
{
    use HasTemplatesSettings;
    use HasPostfixPrefixSettings;

    public $min = null;
    public $max = null;

    public function rules()
    {
        $rules = parent::rules();
        $rules['minMaxDefault'] = [['min', 'max'], 'default', 'value' => null];
        $rules['minMaxNumber'] = [['min', 'max'], 'number'];
        $rules['prefixPostfixDefault'] = [['input_prefix', 'input_postfix'], 'default', 'value' => null];
        $rules['prefixPostfixString'] = [['input_prefix', 'input_postfix'], 'string', 'max' => 32];

        $this->appendRules($rules);

        return $rules;
    }

    public function attributeLabels()
    {
        $labels = parent::attributeLabels();
        $labels['min'] = 'Мин. значение';
        $labels['max'] = 'Макс. значение';
        $labels['input_prefix'] = 'Префикс';
        $labels['input_postfix'] = 'Постфикс';

        $this->appendLabels($labels);

        return $labels;
    }
}
