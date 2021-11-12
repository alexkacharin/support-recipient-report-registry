<?php

/** @noinspection PhpHierarchyChecksInspection */

namespace Matodor\RegistryConstructor\models\data\Email;

use Matodor\RegistryConstructor\models\data\String\Settings as StringSettings;

class Settings extends StringSettings
{
    public function attributeLabels()
    {
        $labels = parent::attributeLabels();
        $labels['min_length'] = 'Мин. длина почты';
        $labels['max_length'] = 'Макс. длина почты';

        return $labels;
    }
}
