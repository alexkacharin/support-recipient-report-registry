<?php

namespace Matodor\RegistryConstructor\models\data\Date;

use Carbon\Carbon;
use Matodor\RegistryConstructor\models\TableRecordValue;
use Matodor\RegistryConstructor\queries\TableRecordQuery;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\Query;
use yii\helpers\Html;

/**
 * @property string|null $value_date
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
        return '{{%registry_table_records_data_date}}';
    }

    public static function additionalColumns()
    {
        return [
            'value_date',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = parent::rules();
        $rules['valueDateDefault'] = ['value_date', 'default', 'value' => null];
        $rules['valueDateDate'] = ['value_date', 'date', 'format' => 'php:Y-m-d'];

        return $rules;
    }

    /**
     * {@inheritdoc}
     * @noinspection DuplicatedCode
     */
    public function attributeLabels()
    {
        $labels = parent::attributeLabels();
        $labels['value_date'] = Html::encode($this->field->name);

        return $labels;
    }

    public function getRecordsQuery()
    {
        return parent::getRecordsQuery()
            ->andFilterWhere(['value_date' => $this->value_date]);
    }

    public function getIsValueSet()
    {
        return $this->value_date !== null;
    }

    public function getExportValue($emptyValue = null)
    {
        if ($this->getIsValueSet()) {
            // TODO
            return Yii::$app->formatter->asDate($this->value_date, 'php:d.m.Y');
        } else {
            return $emptyValue ?? '';
        }
    }

    /**
     * @inheritDoc
     */
    public static function getSelectTitle()
    {
        return 'Дата';
    }

    /**
     * @throws InvalidConfigException
     */
    public function loadFromImport($value)
    {
        if (is_string($value)) {
            // TODO
            $this->value_date = Yii::$app->formatter->asDate(trim($value), 'php:Y-m-d');
        } else {
            $this->addError('value_date', 'Неправильный формат даты');
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
        $subQuery->addSelect(["value_date as {$columnAlias}"]);
        $recordsQuery->addOrderBy([$columnAlias => $sort]);
        return $subQuery;
    }

    /**
     * @return string|null
     */
    public function getRawValue()
    {
        return $this->value_date;
    }

    /**
     * @return string
     */
    public function getFormattedValue()
    {
        return Carbon::createFromFormat('Y-m-d', $this->getRawValue())
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
