<?php

namespace Matodor\Common\behaviors;

use Matodor\Common\models\BaseModel;

class AttributeTypecastBehavior extends \yii\behaviors\AttributeTypecastBehavior
{
    /**
     * @var bool
     */
    public $typecastAfterLoad = true;

    /**
     * @var bool
     */
    public $setNullOnEmpty = true;

    public function events()
    {
        $events = parent::events();

        if ($this->typecastAfterLoad) {
            $events[BaseModel::EVENT_AFTER_LOAD] = 'afterLoad';
        }

        return $events;
    }

    /**
     * Handles owner 'afterLoad' events, ensuring attribute typecasting.
     */
    public function afterLoad()
    {
        if (!$this->owner->hasErrors()) {
            $this->typecastAttributes();
        }
    }

    protected function typecastValue($value, $type)
    {
        switch ($type) {
            case self::TYPE_FLOAT:
            case self::TYPE_INTEGER:
            case self::TYPE_BOOLEAN:
                if ($this->setNullOnEmpty && $this->isEmpty($value)) {
                    return null;
                }

                break;
        }

        return parent::typecastValue($value, $type);
    }

    /**
     * @param mixed $value
     *
     * @return boolean
     */
    public function isEmpty($value)
    {
        return $value === null || $value === [] || $value === '';
    }
}
