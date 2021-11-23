<?php

namespace Matodor\RegistryConstructor\models;

use Matodor\Common\models\BaseModel;
use Matodor\RegistryConstructor\models\forms\TableFieldForm;
use Matodor\RegistryConstructor\models\forms\TableFieldSettingsForm;
use Matodor\RegistryConstructor\queries\TableFieldQuery;
use Matodor\RegistryConstructor\queries\TableRecordQuery;
use Matodor\RegistryConstructor\traits\HasModulePropertiesTrait;
use yii\base\Exception;
use yii\db\ActiveQuery;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * @property integer $id
 * @property integer $registry_table_record_id
 * @property integer $registry_table_field_id
 *
 * @property-read TableRecord|null $record
 * @property-read TableField|null $field
 * @property-read TableFieldSettingsForm|null|TableFieldSettings $fieldSettings
 * @property-read ActiveQuery $recordsQuery
 * @property-read mixed $displayValue
 * @property-read mixed $exportValue
 * @property-read bool $isRequiredValue
 * @property-read bool $hasFieldSettings
 * @property-read bool $isValueSet
 * @property-read string|mixed $formattedValue
 * @property-read string|mixed $valueWithMarkup
 * @property-read string|mixed $rawValue
 */
abstract class TableRecordValue extends BaseModel
{
    use HasModulePropertiesTrait;

    /**
     * TODO: remove this later, when implement all field relations stuff
     * @var bool
     */
    public static $checkFieldRelation = true;

    /**
     * @param TableField $field
     */
    public function setField(TableField $field)
    {
        $this->populateRelation('field', $field);
    }

    /**
     * Get records query by this table record value instance
     *
     * @return ActiveQuery
     */
    public function getRecordsQuery()
    {
        $query = static::find();
        $query->select(['registry_table_record_id']);
        $query->andWhere(['registry_table_field_id' => $this->registry_table_field_id]);
        $query->groupBy(['registry_table_record_id']);

        return $query;
    }

    /**
     * @return array
     */
    public static function additionalColumns()
    {
        return [];
    }

    /**
     * @param TableRecordValue $record
     * @param array $row
     */
    public static function populateRecordFromUnion(TableRecordValue $record, $row)
    {
        foreach (array_values(static::additionalColumns()) as $index => $col) {
            $generatedColName = "col_$index";
            $row[$col] = ArrayHelper::remove($row, $generatedColName);
        }

        parent::populateRecord($record, $row);
    }

    /**
     * @return array
     */
    public static function commonColumns()
    {
        return [
            'id',
            'registry_table_record_id',
            'registry_table_field_id',
            'created_at',
            'updated_at',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
            [['registry_table_record_id', 'registry_table_field_id'], 'required', 'except' => [static::SCENARIO_SEARCH]],
            [['registry_table_record_id', 'registry_table_field_id'], 'integer'],
            [['registry_table_record_id'], 'exist', 'targetClass' => TableRecord::class, 'targetAttribute' => 'id'],
            [['registry_table_field_id'], 'exist', 'targetClass' => TableField::class, 'targetAttribute' => 'id'],
        ];

        if ($this->getIsRequiredValue()) {
            foreach (static::additionalColumns() as $column) {
                $rules["{$column}Required"] = [$column, 'required', 'except' => [static::SCENARIO_SEARCH]];
            }
        }

        return $rules;
    }

    /**
     * @return bool
     * @noinspection PhpUnused
     */
    public function getIsRequiredValue()
    {
        return $this->scenario !== static::SCENARIO_SEARCH
            && $this->field !== null
            && $this->field instanceof TableFieldForm === false
            && $this->field->required;
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[static::SCENARIO_DEFAULT][] = '!registry_table_record_id';

        return $scenarios;
    }

    /**
     * {@inheritdoc}
     * @noinspection DuplicatedCode
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Идентификатор',
            'registry_table_record_id' => 'ID записи',
            'registry_table_field_id' => 'ID поля таблицы',
        ];
    }

    /**
     * @return TableRecordQuery
     * @noinspection PhpUnused
     * @noinspection PhpIncompatibleReturnTypeInspection
     * @noinspection PhpReturnDocTypeMismatchInspection
     */
    public function getRecord()
    {
        return $this->hasOne(TableRecord::class, ['id' => 'registry_table_record_id']);
    }

    /**
     * @return TableFieldQuery
     * @noinspection PhpUnused
     * @noinspection PhpIncompatibleReturnTypeInspection
     * @noinspection PhpReturnDocTypeMismatchInspection
     */
    public function getField()
    {
        return $this->hasOne(TableField::class, ['id' => 'registry_table_field_id']);
    }

    /**
     * @param mixed $value
     */
    public function loadFromImport($value)
    {

    }

    /**
     * TODO DEPRECATED
     * @return bool
     */
    public abstract function getIsValueSet();

    /**
     * @return string
     * @throws Exception
     */
    public static function getSelectTitle()
    {
        throw new Exception('Not implemented');
    }

    /**
     * @param TableRecordQuery $recordsQuery
     * @param int $fieldId
     * @param int $sort SORT_ASC or SORT_DESC
     *
     * @return void
     * @throws Exception
     */
    public static function setupRecordsQueryOrder(
        TableRecordQuery $recordsQuery,
        int $fieldId,
        int $sort
    ) {
        $subQuery = static::find()
            ->select(['[[registry_table_record_id]]'])
            ->where(['[[registry_table_field_id]]' => $fieldId]);
        $subQuery = static::setupRecordsQuerySubQueryOrder($recordsQuery, $subQuery, $sort);
        $subQueryAlias = uniqid('a');

        $recordsQuery->leftJoin([$subQueryAlias => $subQuery],
            "[[{$recordsQuery->alias}]].[[id]] = [[{$subQueryAlias}]].[[registry_table_record_id]]");
    }

    /**
     * @param TableRecordQuery $recordsQuery
     * @param Query $subQuery
     * @param int $sort
     *
     * @return Query
     * @throws Exception
     */
    public static function setupRecordsQuerySubQueryOrder(
        TableRecordQuery $recordsQuery,
        Query $subQuery,
        int $sort
    ) {
        throw new Exception('Not implemented');
    }

    public function __get($name)
    {
        if (static::$checkFieldRelation
            && $name === 'field'
            && $this->isRelationPopulated('field') === false
        ) {
            throw new Exception('Relation `field` not populated');
        }

        return parent::__get($name);
    }

    /**
     * TODO DEPRECATED
     * @return mixed
     */
    public function getExportValue()
    {
        return strip_tags($this->getFormattedValue());
    }

    /**
     * @return mixed
     */
    public abstract function getRawValue();

    /**
     * @return mixed
     */
    public function getFormattedValue()
    {
        return $this->getRawValue();
    }

    /**
     * Get table record field value displayed in records grid or records cards
     * (may contain html)
     *
     * @return mixed
     */
    public function getValueWithMarkup()
    {
        return $this->getFormattedValue();
    }

    /**
     * @return bool
     */
    public function getHasFieldSettings()
    {
        return $this->field->getHasSettings();
    }

    /**
     * @return forms\TableFieldSettingsForm|TableFieldSettings|null
     */
    public function getFieldSettings()
    {
        return $this->field->settings;
    }
}
