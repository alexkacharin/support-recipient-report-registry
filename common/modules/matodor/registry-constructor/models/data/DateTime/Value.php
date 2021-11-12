<?php

namespace Matodor\RegistryConstructor\models\data\DateTime;

use Carbon\Carbon;
use Matodor\RegistryConstructor\models\TableRecordValue;
use Matodor\RegistryConstructor\queries\TableRecordQuery;
use Yii;
use yii\db\Query;
use yii\helpers\Html;

/**
 * @property string|null $value_datetime
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
        return '{{%registry_table_records_data_datetime}}';
    }

    public static function additionalColumns()
    {
        return [
            'value_datetime',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = parent::rules();
        $rules['valueDatetimeDefault'] = ['value_datetime', 'default', 'value' => null];
        $rules['valueDatetimeDatetime'] = ['value_datetime', 'datetime', 'format' => 'php:Y-m-d H:i:s'];

        return $rules;
    }

    /**
     * {@inheritdoc}
     * @noinspection DuplicatedCode
     */
    public function attributeLabels()
    {
        $labels = parent::attributeLabels();
        $labels['value_datetime'] = Html::encode($this->field->name);

        return $labels;
    }

    public function getRecordsQuery()
    {
        return parent::getRecordsQuery()
            ->andFilterWhere(['value_datetime' => $this->value_datetime]);
    }

    public function getIsValueSet()
    {
        return $this->value_datetime !== null;
    }

    public function getExportValue($emptyValue = null)
    {
        if ($this->getIsValueSet()) {
            return Yii::$app->formatter->asDatetime($this->value_datetime, 'php:d.m.Y H:i:s');
        } else {
            return $emptyValue ?? '';
        }
    }

    /**
     * @inheritDoc
     */
    public static function getSelectTitle()
    {
        return 'Дата-время';
    }

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function loadFromImport($value)
    {
        if (is_string($value)) {
            $this->value_datetime = Yii::$app->formatter->asDate(trim($value), 'php:Y-m-d H:i:s');
        } else {
            $this->addError('value_datetime', 'Неправильный формат даты-времени');
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
        $subQuery->addSelect(["value_datetime as {$columnAlias}"]);
        $recordsQuery->addOrderBy([$columnAlias => $sort]);
        return $subQuery;
    }

    /**
     * @return string|null
     */
    public function getRawValue()
    {
        return $this->value_datetime;
    }

    /**
     * @return string
     */
    public function getFormattedValue()
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $this->getRawValue())
            ->locale(Yii::$app->formatter->locale)
            ->translatedFormat($this->getFieldSettings()->format);
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
