<?php

namespace Matodor\Common\traits;

use yii\base\Model;
use yii\helpers\Html;

trait HasParentFormNameTrait
{
    /**
     * @var Model
     */
    public $parentModel = null;

    /**
     * @var string
     */
    public $parentModelAttribute = null;

    /**
     * @return string
     */
    public function parentFormName()
    {
        return Html::getInputName($this->parentModel, $this->parentModelAttribute);
    }
}
