<?php

namespace Matodor\RegistryConstructor\models;

use Matodor\Common\behaviors\AttributeTypecastBehavior;
use Matodor\Common\components\Helper;
use Matodor\RegistryConstructor\models\forms\TableFieldForm;
use Matodor\RegistryConstructor\traits\HasModulePropertiesTrait;
use yii\base\Model;

/**
 * @property TableFieldForm|TableField $field
 */
abstract class TableFieldSettings extends Model
{
    use HasModulePropertiesTrait;

    /**
     * @var TableField|TableFieldForm
     */
    private $_field;

    /**
     * @return array
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['typecast'] = [
            'class' => AttributeTypecastBehavior::class,
        ];

        return $behaviors;
    }

    /**
     * @param TableField $field
     */
    public function setField(TableField $field)
    {
        $this->_field = $field;
    }

    /**
     * @return TableFieldForm|TableField
     */
    public function getField()
    {
        return $this->_field;
    }

    /**
     * @return false|string
     */
    public function toJson()
    {
        $data = [];

        foreach ($this->attributes() as $attribute) {
            if (Helper::isEmpty($this->$attribute)) {
                $data[$attribute] = null;
            } else {
                $data[$attribute] = $this->$attribute;
            }
        }

        return json_encode($data, JSON_FORCE_OBJECT);
    }

    /**
     * @param string|null $json
     *
     * @return bool
     */
    public function fill(?string $json)
    {
        if ($json === null) {
            return false;
        }

        $data = json_decode($json, true);

        if ($data === null) {
            return false;
        }

        if ($this->beforeFill($data) === false) {
            return false;
        }

        foreach ($this->attributes() as $attribute) {
            if (isset($data[$attribute])) {
                $this->$attribute = $data[$attribute];
            }
        }

        $this->afterFill();
        return true;
    }

    public function load($data, $formName = null)
    {
        if (!parent::load($data, $formName)) {
            return false;
        }

        if (!$this->hasErrors()) {
            $this->getBehavior('typecast')->typecastAttributes();
        }

        return true;
    }

    /**
     * @param array $data
     *
     * @return bool
     */
    protected function beforeFill(array &$data)
    {
        return true;
    }

    /**
     * @return void
     */
    protected function afterFill()
    {

    }
}
