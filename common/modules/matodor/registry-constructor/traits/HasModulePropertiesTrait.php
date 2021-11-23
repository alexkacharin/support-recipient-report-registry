<?php

namespace Matodor\RegistryConstructor\traits;

use Matodor\RegistryConstructor\Module;

/**
 * @property-read Module|null $module
 */
trait HasModulePropertiesTrait
{
    /**
     * @return Module|\yii\base\Module|null
     */
    public function getModule()
    {
        return Module::getInstance();
    }
}
