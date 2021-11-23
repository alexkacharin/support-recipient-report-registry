<?php

/** @noinspection PhpHierarchyChecksInspection */

namespace Matodor\RegistryConstructor\models\data\String;

use Matodor\Common\components\Helper;
use Matodor\RegistryConstructor\traits\TableRecordValueFormTrait;

/**
 * @mixin TableRecordValueFormTrait
 */
class ValueForm extends Value
{
    use TableRecordValueFormTrait;

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        if ($this->field->getHasSettings()
            && $this->field->settings instanceof Settings
            && $this->field->settings->allow_html
            && !Helper::isEmpty($this->value_string)
        ) {
            $this->value_string = Helper::closeTags($this->value_string);
        }

        return true;
    }
}
