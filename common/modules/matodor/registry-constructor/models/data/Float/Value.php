<?php

namespace Matodor\RegistryConstructor\models\data\Float;

use Matodor\RegistryConstructor\models\TableRecordValue;
use Matodor\RegistryConstructor\queries\TableRecordQuery;
use Yii;
use yii\db\Query;
use yii\helpers\Html;

/**
 * @property float|null $value_float
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
        return '{{%registry_table_records_data_float}}';
    }

    public static function additionalColumns()
    {
        return [
            'value_float',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = parent::rules();
        $rules['valueFloatDefault'] = ['value_float', 'default', 'value' => null];
        $rules[] = [
            'value_float',
            'number',
            'min' => $this->field->settings->min,
            'max' => $this->field->settings->max,
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
        $labels['value_float'] = Html::encode($this->field->name);

        return $labels;
    }

    public function getRecordsQuery()
    {
        return parent::getRecordsQuery()
            ->andFilterWhere(['value_float' => $this->value_float]);
    }

    public function getIsValueSet()
    {
        return $this->value_float !== null;
    }

    /**
     * @inheritDoc
     */
    public static function getSelectTitle()
    {
        return 'Дробное число';
    }

    public function loadFromImport($value)
    {
        $this->value_float = $value;
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
        $subQuery->addSelect(["value_float as {$columnAlias}"]);
        $recordsQuery->addOrderBy([$columnAlias => $sort]);
        return $subQuery;
    }

    /**
     * @return float|null
     */
    public function getRawValue()
    {
        return $this->value_float;
    }

    /**
     * @return string
     */
    public function getFormattedValue()
    {
        $content = $this
            ->getFieldSettings()
            ->wrapValueWithPrefixPostfix(Yii::$app->formatter->asDecimal($this->getRawValue()));

        return $this
            ->getFieldSettings()
            ->getFormattedValue($this, $content);
    }

    /**
     * @return mixed|string
     */
    public function getValueWithMarkup()
    {
        return $this
            ->getFieldSettings()
            ->getFormattedTableValue($this, $this->getFormattedValue());
    }
}
