<?php

/** @noinspection PhpHierarchyChecksInspection */

namespace Matodor\RegistryConstructor\models\data\String;

use Matodor\RegistryConstructor\models\data\HasPostfixPrefixSettings;
use Matodor\RegistryConstructor\models\data\HasTemplatesSettings;
use Matodor\RegistryConstructor\models\forms\TableFieldSettingsForm;

class Settings extends TableFieldSettingsForm
{
    use HasTemplatesSettings;
    use HasPostfixPrefixSettings;

    public $min_length = null;
    public $max_length = null;
    public $only_digits = false;
    public $allow_html = false;

    public function rules()
    {
        $rules = parent::rules();
        $rules['minMaxLengthDefault'] = [['min_length', 'max_length'], 'default', 'value' => null];
        $rules['minMaxLengthInteger'] = [['min_length', 'max_length'], 'integer', 'min' => 0, 'max' => 255];

        $rules['onlyDigitsAllowHtmlDefault'] = [['only_digits', 'allow_html'], 'default', 'value' => false];
        $rules['onlyDigitsAllowHtmlBoolean'] = [['only_digits', 'allow_html'], 'boolean'];

        $rules['prefixPostfixDefault'] = [['input_prefix', 'input_postfix'], 'default', 'value' => null];
        $rules['prefixPostfixString'] = [['input_prefix', 'input_postfix'], 'string', 'max' => 32];

        $this->appendRules($rules);

        return $rules;
    }

    public function attributeLabels()
    {
        $labels = parent::attributeLabels();
        $labels['min_length'] = 'Мин. длина строки';
        $labels['max_length'] = 'Макс. длина строки';
        $labels['only_digits'] = 'Только числа';
        $labels['allow_html'] = 'Не преобразовывать специальные символы в HTML-сущности';
        $labels['input_prefix'] = 'Префикс';
        $labels['input_postfix'] = 'Постфикс';

        $this->appendLabels($labels);

        return $labels;
    }
}
