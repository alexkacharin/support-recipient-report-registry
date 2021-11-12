<?php

namespace Matodor\RegistryConstructor\models\data\String;

use Matodor\Common\components\Helper;
use Matodor\RegistryConstructor\models\TableRecordValue;
use Matodor\RegistryConstructor\queries\TableRecordQuery;
use yii\db\Query;
use yii\helpers\Html;

/**
 * @property string $value_string
 *
 * @method Settings getFieldSettings()
 */
class Value extends TableRecordValue
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%registry_table_records_data_string}}';
    }

    public static function additionalColumns()
    {
        return [
            'value_string',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = parent::rules();
        $rules['valueStringDefault'] = ['value_string', 'default', 'value' => null];
        $rules['valueStringTrim'] = ['value_string', 'filter', 'filter' => 'trim'];
        $rules['valueStringString'] = [
            'value_string',
            'string',
            'min' => $this->getFieldSettings()->min_length,
            'max' => $this->getFieldSettings()->max_length,
        ];

        return $rules;
    }

    /**
     * {@inheritdoc}
     * @noinspection DuplicatedCode
     */
    public function attributeLabels()
    {
        $labels = parent::attributeLabels();
        $labels['value_string'] = Html::encode($this->field->name);

        return $labels;
    }

    public function getRecordsQuery()
    {
        return parent::getRecordsQuery()
            ->andFilterWhere(['like', 'value_string', $this->value_string]);
    }

    public function getIsValueSet()
    {
        return !Helper::isEmpty($this->value_string);
    }

    /**
     * @inheritDoc
     */
    public static function getSelectTitle()
    {
        return 'Строка (до 255 символов)';
    }

    public function loadFromImport($value)
    {
        $this->value_string = (string) $value;
    }

    /**
     * @inheritDoc
     */
    public static function setupRecordsQuerySubQueryOrder(
        TableRecordQuery $recordsQuery,
        Query $subQuery,
        int $sort
    ) {
        $columnAlias = uniqid('col');
        $subQuery->addSelect(["value_string as {$columnAlias}"]);
        $recordsQuery->addOrderBy([$columnAlias => $sort]);
        return $subQuery;
    }

    /**
     * @return string
     */
    public function getRawValue()
    {
        return $this->value_string;
    }

    /**
     * @return string
     */
    public function getFormattedValue()
    {
        $content = $this->getFieldSettings()->allow_html
            ? $this->getRawValue()
            : Html::encode($this->getRawValue());

        $content = $this
            ->getFieldSettings()
            ->wrapValueWithPrefixPostfix($content);

        return $this
            ->getFieldSettings()
            ->getFormattedValue($this, $content);
    }

    public function getValueWithMarkup()
    {
        return $this
            ->getFieldSettings()
            ->getFormattedTableValue($this, $this->getFormattedValue());
    }
}
