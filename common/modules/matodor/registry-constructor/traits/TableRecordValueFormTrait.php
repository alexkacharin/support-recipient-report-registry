<?php

namespace Matodor\RegistryConstructor\traits;

use Matodor\Common\traits\HasParentFormNameTrait;
use Matodor\RegistryConstructor\models\TableRecordValue;
use yii\helpers\Inflector;

/**
 * @mixin TableRecordValue
 */
trait TableRecordValueFormTrait
{
    use HasParentFormNameTrait;

    /**
     * @var bool
     */
    public $isInSearchForm = false;

    /**
     * @return string
     */
    public function formName()
    {
        return $this->parentFormName() . "[{$this->registry_table_field_id}]";
    }

    /**
     * @return string
     * @noinspection PhpUnused
     */
    public function getFormView()
    {
        $viewFile = Inflector::camel2id($this->field->value_type);
        return "/records/form-fields-by-type/_field-{$viewFile}.php";
    }
}
