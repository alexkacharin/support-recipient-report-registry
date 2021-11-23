<?php

/** @noinspection PhpHierarchyChecksInspection */

namespace Matodor\RegistryConstructor\models\data\Date;

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

        if (!Helper::isEmpty($this->value_date)) {
            $dateTime = DateTime::createFromFormat('d.m.Y', $this->value_date);

            if ($dateTime !== false) {
                $this->value_date = $dateTime->format('Y-m-d');
            }
        }

        return true;
    }

    public function afterFind()
    {
        parent::afterFind();

        if (!Helper::isEmpty($this->value_date)) {
            $dateTime = DateTime::createFromFormat('Y-m-d', $this->value_date);

            if ($dateTime !== false) {
                $this->value_date = $dateTime->format('d.m.Y');
            }
        }
    }
}
