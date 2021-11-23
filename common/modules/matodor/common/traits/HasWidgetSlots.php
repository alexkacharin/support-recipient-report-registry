<?php

namespace Matodor\Common\traits;

use yii\base\Exception;

trait HasWidgetSlots
{
    /**
     * @var string[]|array
     */
    public $slotsContent = [];

    /**
     * @param string $slotName
     */
    public function beginSlot(string $slotName)
    {
        ob_start();
        ob_implicit_flush(false);

        $this->slotsContent[$slotName] = false;
    }

    /**
     * @param string $slotName
     */
    public function endSlot(string $slotName)
    {
        $this->slotsContent[$slotName] = ob_get_clean();
    }

    /**
     * @param string $slotName
     *
     * @return bool
     */
    public function hasSlot(string $slotName)
    {
        return isset($this->slotsContent[$slotName]);
    }

    /**
     * @param string $slotName
     *
     * @return mixed|string
     * @throws Exception
     */
    public function getSlot(string $slotName)
    {
        $content = $this->slotsContent[$slotName];

        if ($content === false) {
            if (ob_get_level() > 0) {
                ob_end_clean();
            }

            throw new Exception('End slot before get slot content');
        }

        return $content;
    }
}
