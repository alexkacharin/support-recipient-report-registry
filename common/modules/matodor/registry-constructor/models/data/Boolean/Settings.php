<?php

/** @noinspection PhpHierarchyChecksInspection */

namespace Matodor\RegistryConstructor\models\data\Boolean;

use Matodor\RegistryConstructor\models\data\HasTemplatesSettings;
use Matodor\RegistryConstructor\models\forms\TableFieldSettingsForm;

/**
 * @property-read mixed $formatsSelectData
 */
class Settings extends TableFieldSettingsForm
{
    use HasTemplatesSettings;

    public function rules()
    {
        $rules = parent::rules();
        $this->appendRules($rules);
        return $rules;
    }

    public function attributeLabels()
    {
        $labels = parent::attributeLabels();
        $this->appendLabels($labels);
        return $labels;
    }
}
