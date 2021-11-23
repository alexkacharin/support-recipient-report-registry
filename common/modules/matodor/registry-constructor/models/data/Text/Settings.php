<?php

/** @noinspection PhpHierarchyChecksInspection */

namespace Matodor\RegistryConstructor\models\data\Text;

use Matodor\RegistryConstructor\models\data\HasTemplatesSettings;
use Matodor\RegistryConstructor\models\forms\TableFieldSettingsForm;

class Settings extends TableFieldSettingsForm
{
    use HasTemplatesSettings;

    public $min_length = null;
    public $max_length = null;
    public $allow_html = false;

    public function rules()
    {
        $rules = parent::rules();
        $rules['minMaxLengthDefault'] = [['min_length', 'max_length'], 'default', 'value' => null];
        $rules['minMaxLengthInteger'] = [['min_length', 'max_length'], 'integer', 'min' => 0];

        $rules['allowHtmlDefault'] = ['allow_html', 'default', 'value' => false];
        $rules['allowHtmlBoolean'] = ['allow_html', 'boolean'];

        $this->appendRules($rules);

        return $rules;
    }

    public function attributeLabels()
    {
        $labels = parent::attributeLabels();
        $labels['min_length'] = 'Мин. длина строки';
        $labels['max_length'] = 'Макс. длина строки';
        $labels['allow_html'] = 'Не преобразовывать специальные символы в HTML-сущности';

        $this->appendLabels($labels);

        return $labels;
    }
}
