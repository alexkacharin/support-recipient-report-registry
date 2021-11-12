<?php

namespace Matodor\RegistryConstructor\components;

use Matodor\RegistryConstructor\models\TableFieldSettings;
use Matodor\RegistryConstructor\models\TableRecordValue;
use Matodor\RegistryConstructor\traits\TableRecordValueFormTrait;
use yii\base\Component;

/**
 * @property-read bool $hasSettings
 */
class ValueTypeDefinition extends Component
{
    /**
     * @var string
     */
    public $type = null;

    /**
     * @var string|TableRecordValue
     */
    public $valueClass = null;

    /**
     * @var string|TableRecordValueFormTrait
     */
    public $valueFormClass = null;

    /**
     * @var string
     */
    public $valueTypeUnderscored = null;

    /**
     * @var string|TableFieldSettings
     */
    public $settingsClass = null;

    /**
     * @return bool
     */
    public function getHasSettings()
    {
        return $this->settingsClass !== null;
    }
}
