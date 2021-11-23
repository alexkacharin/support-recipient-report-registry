<?php

namespace Matodor\RegistryConstructor\models\data\Boolean;

use Matodor\RegistryConstructor\models\TableRecordValue;
use Matodor\RegistryConstructor\queries\TableRecordQuery;
use Yii;
use yii\db\Query;
use yii\helpers\Html;

/**
 * @property boolean $value_boolean
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
        return '{{%registry_table_records_data_boolean}}';
    }

    public static function additionalColumns()
    {
        return [
            'value_boolean',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = parent::rules();
        $rules['valueBooleanDefault'] = ['value_boolean', 'default', 'value' => null];
        $rules['valueBooleanBoolean'] = ['value_boolean', 'boolean'];

        return $rules;
    }

    /**
     * {@inheritdoc}
     * @noinspection DuplicatedCode
     */
    public function attributeLabels()
    {
        $labels = parent::attributeLabels();
        $labels['value_boolean'] = Html::encode($this->field->name);

        return $labels;
    }

    public function getRecordsQuery()
    {
        return parent::getRecordsQuery()
            ->andFilterWhere(['value_boolean' => $this->value_boolean]);
    }

    /**
     * @return bool
     */
    public function getIsValueSet()
    {
        return $this->value_boolean !== null;
    }

    /**
     * @inheritDoc
     */
    public static function getSelectTitle()
    {
        return 'Логическое (Да / Нет)';
    }

    public function loadFromImport($value)
    {
        if ($value === 'Да') {
            $this->value_boolean = true;
        } else if ($value === 'Нет') {
            $this->value_boolean = false;
        } else if ($value === '+') {
            $this->value_boolean = true;
        } else if ($value === '-') {
            $this->value_boolean = false;
        } else {
            $this->value_boolean = (bool) $value;
        }
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
        $subQuery->addSelect(["value_boolean as {$columnAlias}"]);
        $recordsQuery->addOrderBy([$columnAlias => $sort]);
        return $subQuery;
    }

    /**
     * @return bool
     */
    public function getRawValue()
    {
        return $this->value_boolean;
    }

    /**
     * @return string
     */
    public function getFormattedValue()
    {
        return $this
            ->getFieldSettings()
            ->getFormattedValue($this, Yii::$app->formatter->asBoolean($this->getRawValue()));
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
