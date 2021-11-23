<?php

namespace Matodor\RegistryConstructor\events;

class LoadEvent extends \yii\base\ModelEvent
{
    /**
     * @var array
     */
    public $loadData = [];
}
