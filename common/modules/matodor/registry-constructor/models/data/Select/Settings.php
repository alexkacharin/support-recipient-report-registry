<?php

/** @noinspection DuplicatedCode */
/** @noinspection PhpHierarchyChecksInspection */

namespace Matodor\RegistryConstructor\models\data\Select;

use Matodor\RegistryConstructor\models\forms\TableFieldSettingsForm;
use Matodor\RegistryConstructor\models\TableField;
use Matodor\RegistryConstructor\models\TableRecord;
use Matodor\RegistryConstructor\models\TableRecordValue;
use Yii;
use yii\base\Exception;
use yii\base\InvalidConfigException;

class Settings extends TableFieldSettingsForm
{
    /**
     * @var string|null
     */
    public $template = null;

    /**
     * @var string|null
     */
    public $template_in_table = null;

    protected function afterFill()
    {
        parent::afterFill();

        if ($this->template === null) {
            $this->initDefaultTemplate();
        }
    }

    protected function initDefaultTemplate()
    {
        $this->template = join(', ', array_map(function ($variantField) {
            return $this->getFieldNameTemplate($variantField);
        }, $this->field->variantsTable->fields));
    }

    public function rules()
    {
        $rules = parent::rules();
        $rules['templateRequired'] = ['template', 'required'];
        $rules['templateDefault'] = ['template', 'default', 'value' => null];
        $rules['templateString'] = ['template', 'string'];
        $rules['templateTrim'] = ['template', 'filter', 'filter' => 'trim'];

        $rules['templateInTableDefault'] = ['template_in_table', 'default', 'value' => null];
        $rules['templateInTableString'] = ['template_in_table', 'string'];
        $rules['templateInTableTrim'] = ['template_in_table', 'filter', 'filter' => 'trim'];

        return $rules;
    }

    public function attributeLabels()
    {
        $labels = parent::attributeLabels();
        $labels['template'] = 'Шаблон отображения';
        $labels['template_in_table'] = 'Шаблон отображения в таблице';

        return $labels;
    }

    /**
     * @param TableRecord $variantRecord
     *
     * @return string
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function getFormattedValue(TableRecord $variantRecord)
    {
        /** @noinspection RegExpRedundantEscape */
        return preg_replace_callback('/\$\{(.*)\}/Ui', function ($matches) use ($variantRecord) {
            $field = $this->field->variantsTable->getFieldByName($matches[1]);

            if ($field === null) {
                return $matches[0];
            }

            if ($field->registry_table_id != $variantRecord->registry_table_id) {
                throw new Exception('`variantRecord` is not in field->variantsTable');
            }

            $fieldValue = $variantRecord->getValueOrInstantiate($field);
            return $fieldValue->getIsValueSet()
                ? $fieldValue->getFormattedValue()
                : Yii::t('yii', '(not set)', [], Yii::$app->language);
        }, $this->template);
    }

    /**
     * @param TableRecordValue $fieldValue
     * @param TableRecord $variantRecord
     * @param mixed $content
     *
     * @return mixed|string
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function getFormattedTableValue(
        TableRecordValue $fieldValue,
        TableRecord $variantRecord,
        $content
    ) {
        if ($this->template_in_table === null
            || $this->template_in_table === ''
        ) {
            return $content;
        }

        if (strncmp($this->template_in_table, 'view:', 5) === 0) {
            $view = substr($this->template_in_table, 5);

            return Yii::$app->view->render($view, [
                'fieldValue' => $fieldValue,
                'variantRecord' => $variantRecord,
            ]);
        } else {
            /** @noinspection RegExpRedundantEscape */
            return preg_replace_callback('/\$\{(.*)\}/Ui', function ($matches) use (
                $content,
                $variantRecord
            ) {
                if ($matches[1] === '<FormattedValue>') {
                    return $content;
                }

                $field = $this->field->variantsTable->getFieldByName($matches[1]);

                if ($field === null) {
                    return $matches[0];
                }

                if ($field->registry_table_id != $variantRecord->registry_table_id) {
                    throw new Exception('`variantRecord` is not in field->variantsTable');
                }

                $value = $variantRecord->getValueOrInstantiate($field);
                return $value->getIsValueSet()
                    ? $value->getFormattedValue()
                    : Yii::t('yii', '(not set)', [], Yii::$app->language);
            }, $this->template_in_table);
        }
    }

    /**
     * @return string
     */
    public function getFieldNameTemplate(TableField $field)
    {
        return "\${{$field->name}}";
    }
}
