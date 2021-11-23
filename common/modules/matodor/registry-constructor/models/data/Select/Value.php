<?php

namespace Matodor\RegistryConstructor\models\data\Select;

use Matodor\RegistryConstructor\models\TableRecord;
use Matodor\RegistryConstructor\models\TableRecordValue;
use Matodor\RegistryConstructor\queries\TableRecordQuery;
use Yii;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\db\Query;
use yii\helpers\Html;

/**
 * @property integer|null $value_record_id
 *
 * @property-read TableRecord|null $valueRecord
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
        return '{{%registry_table_records_data_select}}';
    }

    public static function additionalColumns()
    {
        return [
            'value_record_id',
        ];
    }

    /**
     * @return TableRecordQuery
     * @noinspection PhpUnused
     * @noinspection PhpIncompatibleReturnTypeInspection
     * @noinspection PhpReturnDocTypeMismatchInspection
     */
    public function getValueRecord()
    {
        return $this->hasOne(TableRecord::class, ['id' => 'value_record_id']);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = parent::rules();
        $rules['valueRecordIdDefault'] = ['value_record_id', 'default', 'value' => null];
        $rules['valueRecordIdExist'] = [
            'value_record_id',
            'exist',
            'targetClass' => TableRecord::class,
            'targetAttribute' => 'id',
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
        $labels['value_record_id'] = Html::encode($this->field->name);

        return $labels;
    }

    public function getRecordsQuery()
    {
        return parent::getRecordsQuery()
            ->andFilterWhere(['value_record_id' => $this->value_record_id]);
    }

    public function getIsValueSet()
    {
        return $this->value_record_id !== null
            && $this->value_record_id !== 0;
    }

    /**
     * @inheritDoc
     */
    public static function getSelectTitle()
    {
        return '';
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
        $subQuery->addSelect(["value_record_id as {$columnAlias}"]);
        $recordsQuery->addOrderBy([$columnAlias => $sort]);
        return $subQuery;
    }

    /**
     * @return TableRecord|null
     */
    public function getRawValue()
    {
        return $this->valueRecord;
    }

    /**
     * @return string
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function getFormattedValue()
    {
        return $this
            ->getFieldSettings()
            ->getFormattedValue($this->getRawValue());
    }

    /**
     * @return mixed|string
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function getValueWithMarkup()
    {
        return $this
            ->getFieldSettings()
            ->getFormattedTableValue(
                $this,
                $this->getRawValue(),
                $this->getFormattedValue()
            );
    }
}
