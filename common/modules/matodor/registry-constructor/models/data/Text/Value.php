<?php

namespace Matodor\RegistryConstructor\models\data\Text;

use Matodor\Common\components\Helper;
use Matodor\RegistryConstructor\models\TableRecordValue;
use Matodor\RegistryConstructor\queries\TableRecordQuery;
use yii\db\Query;
use yii\helpers\Html;

/**
 * @property string $value_text
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
        return '{{%registry_table_records_data_text}}';
    }

    public static function additionalColumns()
    {
        return [
            'value_text',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = parent::rules();
        $rules['valueTextDefault'] = ['value_text', 'default', 'value' => null];
        $rules['valueTextTrim'] = ['value_text', 'filter', 'filter' => 'trim'];
        $rules['valueTextString'] = [
            'value_text',
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
        $labels['value_text'] = Html::encode($this->field->name);

        return $labels;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRecordsQuery()
    {
        return parent::getRecordsQuery()
            ->andFilterWhere(['like', 'value_text', $this->value_text]);
    }

    /**
     * @return bool
     */
    public function getIsValueSet()
    {
        return !Helper::isEmpty($this->value_text);
    }

    /**
     * @inheritDoc
     */
    public static function getSelectTitle()
    {
        return 'Текст';
    }

    public function loadFromImport($value)
    {
        $this->value_text = (string) $value;
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
        $subQuery->addSelect(["value_text as {$columnAlias}"]);
        $recordsQuery->addOrderBy([$columnAlias => $sort]);
        return $subQuery;
    }

    /**
     * @return string
     */
    public function getRawValue()
    {
        return $this->value_text;
    }

    /**
     * @return string
     */
    public function getFormattedValue()
    {
        $content = $this->getFieldSettings()->allow_html
            ? $this->getRawValue()
            : Html::encode($this->getRawValue());

        return $this
            ->getFieldSettings()
            ->getFormattedValue($this, $content);
    }

    public function getValueWithMarkup()
    {
        return $this
            ->getFieldSettings()
            ->getFormattedTableValue($this, nl2br($this->getFormattedValue()));
    }
}
