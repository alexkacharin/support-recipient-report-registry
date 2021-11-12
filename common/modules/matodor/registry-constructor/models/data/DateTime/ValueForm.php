<?php

/** @noinspection PhpHierarchyChecksInspection */

namespace Matodor\RegistryConstructor\models\data\DateTime;

use DateTime;
use Matodor\Common\components\Helper;
use Matodor\RegistryConstructor\traits\TableRecordValueFormTrait;

/**
 * @mixin TableRecordValueFormTrait
 */
class ValueForm extends Value
{
    use TableRecordValueFormTrait;

    public function load($data, $formName = null)
    {
        if (!parent::load($data, $formName)) {
            return false;
        }

        if (!Helper::isEmpty($this->value_datetime)) {
            $dateTime = DateTime::createFromFormat('d.m.Y H:i', $this->value_datetime);

            if ($dateTime !== false) {
                $this->value_datetime = $dateTime->format('Y-m-d H:i:s');
            }
        }

        return true;
    }

    public function afterFind()
    {
        parent::afterFind();

        if (!Helper::isEmpty($this->value_datetime)) {
            $dateTime = DateTime::createFromFormat('Y-m-d H:i:s', $this->value_datetime);

            if ($dateTime !== false) {
                $this->value_datetime = $dateTime->format('d.m.Y H:i');
            }
        }
    }
}
