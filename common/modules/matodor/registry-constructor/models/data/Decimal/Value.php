<?php

namespace Matodor\RegistryConstructor\models\data\Decimal;

use Matodor\RegistryConstructor\models\TableRecordValue;
use Matodor\RegistryConstructor\queries\TableRecordQuery;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\Query;
use yii\helpers\Html;

/**
 * @property integer|null $value_decimal
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
        return '{{%registry_table_records_data_decimal}}';
    }

    public static function additionalColumns()
    {
        return [
            'value_decimal',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = parent::rules();
        $rules['valueDecimalDefault'] = ['value_decimal', 'default', 'value' => null];
        $rules['valueDecimalNumber'] = [
            'value_decimal',
            'number',
            'min' => $this->getFieldSettings()->min,
            'max' => $this->getFieldSettings()->max,
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
        $labels['value_decimal'] = Html::encode($this->field->name);

        return $labels;
    }

    public function getRecordsQuery()
    {
        return parent::getRecordsQuery()
            ->andFilterWhere(['value_decimal' => $this->value_decimal]);
    }

    public function getIsValueSet()
    {
        return $this->value_decimal !== null;
    }

    /**
     * @inheritDoc
     */
    public static function getSelectTitle()
    {
        return 'Денежный формат (15 цифр, включая 2 после запятой)';
    }

    public function loadFromImport($value)
    {
        $this->value_decimal = $value;
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
        $subQuery->addSelect(["value_decimal as {$columnAlias}"]);
        $recordsQuery->addOrderBy([$columnAlias => $sort]);
        return $subQuery;
    }

    /**
     * @return int|null
     */
    public function getRawValue()
    {
        return $this->value_decimal;
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
