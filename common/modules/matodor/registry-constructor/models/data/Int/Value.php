<?php

namespace Matodor\RegistryConstructor\models\data\Int;

use Matodor\RegistryConstructor\models\TableRecordValue;
use Matodor\RegistryConstructor\queries\TableRecordQuery;
use Yii;
use yii\db\Query;
use yii\helpers\Html;

/**
 * @property integer|null $value_int
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
        return '{{%registry_table_records_data_int}}';
    }

    public static function additionalColumns()
    {
        return [
            'value_int',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = parent::rules();
        $rules['valueIntDefault'] = ['value_int', 'default', 'value' => null];

        if ($this->field->getHasSettings() && $this->field->settings instanceof Settings) {
            $rules['valueIntInteger'] = [
                'value_int',
                'integer',
                'min' => $this->field->settings->min,
                'max' => $this->field->settings->max,
            ];
        } else {
            $rules['valueIntInteger'] = ['value_int', 'integer'];
        }

        return $rules;
    }

    /**
     * {@inheritdoc}
     * @noinspection DuplicatedCode
     */
    public function attributeLabels()
    {
        $labels = parent::attributeLabels();
        $labels['value_int'] = Html::encode($this->field->name);

        return $labels;
    }

    public function getRecordsQuery()
    {
        return parent::getRecordsQuery()
            ->andFilterWhere(['value_int' => $this->value_int]);
    }

    public function getIsValueSet()
    {
        return $this->value_int !== null;
    }

    /**
     * @inheritDoc
     */
    public static function getSelectTitle()
    {
        return 'Целочисленное число (от -2147483648, до 2147483647)';
    }

    public function loadFromImport($value)
    {
        $this->value_int = $value;
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
        $subQuery->addSelect(["value_int as {$columnAlias}"]);
        $recordsQuery->addOrderBy([$columnAlias => $sort]);
        return $subQuery;
    }

    /**
     * @return float|null
     */
    public function getRawValue()
    {
        return $this->value_int;
    }

    /**
     * @return string
     */
    public function getFormattedValue()
    {
        $content = $this
            ->getFieldSettings()
            ->wrapValueWithPrefixPostfix(Yii::$app->formatter->asInteger($this->getRawValue()));

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
