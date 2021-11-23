<?php

namespace Matodor\RegistryConstructor\models\forms;

use Matodor\Common\components\Helper;
use Matodor\RegistryConstructor\models\TableFieldSettings;
use yii\base\InvalidConfigException;
use yii\helpers\Inflector;
use yii\web\View;

abstract class TableFieldSettingsForm extends TableFieldSettings
{
    /**
     * @param View $view
     *
     * @return string
     * @throws InvalidConfigException
     */
    public function getFieldSettingsView(View $view)
    {
        if (Helper::isEmpty($this->field->value_type)) {
            return null;
        }

        $viewFile = Inflector::camel2id($this->field->value_type);
        $viewFile = "/table-constructor/form/fields-settings/_field-{$viewFile}.php";
        $fullPath = $this->module->getViewPath() . $viewFile;

        if ($view->theme !== null) {
            $fullPath = $view->theme->applyTo($fullPath);
        }

        return is_file($fullPath) ? $viewFile : null;
    }

    public function formName()
    {
        return $this->field->formName() . '[settings]';
    }
}
