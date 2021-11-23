<?php

/** @noinspection DuplicatedCode */

namespace Matodor\RegistryConstructor\models\data;

use Matodor\RegistryConstructor\models\forms\TableFieldSettingsForm;
use Matodor\RegistryConstructor\models\TableRecordValue;
use Yii;

/**
 * @mixin TableFieldSettingsForm
 */
trait HasTemplatesSettings
{
    /**
     * @var string|null
     */
    public $template = null;

    /**
     * @var string|null
     */
    public $template_in_table = null;

    /**
     * @param TableRecordValue $fieldValue
     * @param mixed $content
     *
     * @return mixed|string
     */
    public function getFormattedValue(TableRecordValue $fieldValue, $content)
    {
        if ($this->template === null
            || $this->template === ''
        ) {
            return $content;
        }

        return str_replace('${<Value>}', $content, $this->template);
    }

    /**
     * @param TableRecordValue $fieldValue
     * @param mixed $content
     *
     * @return mixed|string
     */
    public function getFormattedTableValue(TableRecordValue $fieldValue, $content)
    {
        if ($this->template_in_table === null
            || $this->template_in_table === ''
        ) {
            return $content;
        }

        if (strncmp($this->template_in_table, 'view:', 5) === 0) {
            $view = substr($this->template_in_table, 5);

            return Yii::$app->view->render($view, [
                'fieldValue' => $fieldValue,
                'formattedValue' => $content,
            ]);
        } else {
            return str_replace('${<FormattedValue>}', $content, $this->template_in_table);
        }
    }

    protected function appendRules(&$rules)
    {
        $rules['templateDefault'] = ['template', 'default', 'value' => null];
        $rules['templateString'] = ['template', 'string'];
        $rules['templateTrim'] = ['template', 'filter', 'filter' => 'trim'];

        $rules['templateInTableDefault'] = ['template_in_table', 'default', 'value' => null];
        $rules['templateInTableString'] = ['template_in_table', 'string'];
        $rules['templateInTableTrim'] = ['template_in_table', 'filter', 'filter' => 'trim'];
    }

    protected function appendLabels(&$labels)
    {
        $labels['template'] = 'Шаблон отображения (общий)';
        $labels['template_in_table'] = 'Шаблон отображения в таблице';
    }
}
