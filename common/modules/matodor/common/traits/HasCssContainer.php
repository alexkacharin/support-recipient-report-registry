<?php

namespace Matodor\Common\traits;

use Matodor\Common\components\Helper;
use yii\helpers\Html;

trait HasCssContainer
{
    /**
     * @var string
     */
    public $containerCssClass = false;

    /**
     * @param string $content
     * @param string|false $cssClass
     *
     * @return string
     */
    protected function renderContainer(string $content, $cssClass)
    {
        return Html::tag('div', $content, [
            'class' =>  Helper::filterCssClasses([
                $cssClass => true,
                $this->containerCssClass => !!$this->containerCssClass,
            ]),
        ]);
    }
}
