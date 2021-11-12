<?php

/** @noinspection PhpHierarchyChecksInspection */

namespace Matodor\RegistryConstructor\models\data\Email;

use Matodor\RegistryConstructor\traits\TableRecordValueFormTrait;

/**
 * @mixin TableRecordValueFormTrait
 */
class ValueForm extends Value
{
    use TableRecordValueFormTrait;

    public function rules()
    {
        $rules = parent::rules();

        if ($this->isInSearchForm
            && isset($rules['valueStringEmail'])
        ) {
            unset($rules['valueStringEmail']);
        }

        return $rules;
    }
}
