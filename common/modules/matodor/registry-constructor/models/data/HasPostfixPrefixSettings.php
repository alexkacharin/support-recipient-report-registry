<?php

namespace Matodor\RegistryConstructor\models\data;

use Matodor\Common\components\Helper;
use Matodor\RegistryConstructor\models\forms\TableFieldSettingsForm;
use yii\helpers\Html;

/**
 * @mixin TableFieldSettingsForm
 * @property-read bool $hasPrefixOrPostfix
 * @property-read bool $hasPrefix
 * @property-read bool $hasPostfix
 * @property-read string $activeFieldTemplate
 */
trait HasPostfixPrefixSettings
{
    /**
     * @var string|null
     */
    public $input_prefix = null;

    /**
     * @var string|null
     */
    public $input_postfix = null;

    private $_hasPrefix = null;
    private $_hasPostfix = null;

    /**
     * @return bool
     */
    public function getHasPrefixOrPostfix()
    {
        return $this->getHasPrefix() || $this->getHasPostfix();
    }

    /**
     * @return bool
     */
    public function getHasPrefix()
    {
        if ($this->_hasPrefix === null) {
            $this->_hasPrefix = !Helper::isEmpty($this->input_prefix);
        }

        return $this->_hasPrefix;
    }

    /**
     * @return bool
     */
    public function getHasPostfix()
    {
        if ($this->_hasPostfix === null) {
            $this->_hasPostfix = !Helper::isEmpty($this->input_postfix);
        }

        return $this->_hasPostfix;
    }

    /**
     * @return false|string
     */
    public function getActiveFieldTemplate()
    {
        if (!$this->getHasPrefixOrPostfix()) {
            return false;
        }

        $template = "{label}\n";
        $template .= '<div class="input-group">';

        if ($this->getHasPrefix()) {
            $prefix = Html::encode($this->input_prefix);
            $template .= '<div class="input-group-prepend">';
            $template .= "<span class=\"input-group-text\">{$prefix}</span>";
            $template .= '</div>';
        }

        $template .= '{input}';

        if ($this->getHasPostfix()) {
            $postfix = Html::encode($this->input_postfix);
            $template .= '<div class="input-group-append">';
            $template .= "<span class=\"input-group-text\">{$postfix}</span>";
            $template .= '</div>';
        }

        $template .= '</div>';
        $template .= "\n{hint}\n{error}";

        return $template;
    }

    /**
     * @param mixed $value
     *
     * @return mixed|string
     */
    public function wrapValueWithPrefixPostfix($value)
    {
        if ($this->getHasPrefix()) {
            $prefix = Html::encode($this->input_prefix);
            $value = "{$prefix} {$value}";
        }

        if ($this->getHasPostfix()) {
            $postfix = Html::encode($this->input_postfix);
            $value = "{$value} {$postfix}";
        }

        return $value;
    }
}
