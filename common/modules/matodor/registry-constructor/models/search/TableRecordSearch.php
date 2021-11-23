<?php

namespace Matodor\RegistryConstructor\models\search;

use Matodor\RegistryConstructor\data\RecordsDataProvider;
use Matodor\RegistryConstructor\models\data\Boolean\Value as BooleanValue;
use Matodor\RegistryConstructor\models\Table;
use Matodor\RegistryConstructor\models\TableField;
use Matodor\RegistryConstructor\models\TableRecord;
use Matodor\RegistryConstructor\models\TableRecordValue;
use Matodor\RegistryConstructor\queries\TableRecordQuery;
use Matodor\RegistryConstructor\traits\TableRecordValueFormTrait;
use Yii;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;
use yii\db\Exception;
use yii\helpers\ArrayHelper;

class TableRecordSearch extends TableRecord
{
    /**
     * @var TableRecordValue[]|TableRecordValueFormTrait[]
     */
    public $fields = [];

    public function __construct($config = [])
    {
        $this->scenario = static::SCENARIO_SEARCH;
        parent::__construct($config);
    }

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();

        foreach ($this->table->fields as $field) {
            if ($field->hasFlag(TableField::FLAGS_IS_VISIBLE_IN_SEARCH_FORM)) {
                $fieldValue = $field->instantiateRecordValue(true, false);
                $fieldValue->registry_table_field_id = $field->id;
                $fieldValue->scenario = $this->scenario;
                $fieldValue->parentModel = $this;
                $fieldValue->parentModelAttribute = 'f';

                $this->fields[$field->id] = $fieldValue;
            }
        }
    }

    /**
     * @param Table $table
     */
    public function setTable(Table $table)
    {
        $this->populateRelationIfNeeded('table', $table);
        $this->registry_table_id = $table->id;
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[static::SCENARIO_SEARCH][] = '!registry_table_id';
        $scenarios[static::SCENARIO_SEARCH][] = '!f';

        return $scenarios;
    }

    /**
     * @param array $params
     *
     * @return ActiveDataProvider
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function search(array $params)
    {
        if ($this->table === null) {
            throw new Exception('Need populate `table` relation');
        }

        $query = $this->table->getRecords();

        /** @var RecordsDataProvider $dataProvider */
        $dataProvider = Yii::createObject([
            'class' => RecordsDataProvider::class,
            'table' => $this->table,
            'query' => $query,
            'pagination' => [
                'defaultPageSize' => $this->table->default_page_size,
            ],
            'sort' => [
                'defaultOrder' => ['updated_at' => SORT_DESC],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $this->setupQuery($query);
        return $dataProvider;
    }

    /**
     * @param TableRecordQuery $recordsQuery
     *
     * @return TableRecordQuery
     */
    public function setupQuery(TableRecordQuery $recordsQuery)
    {
        foreach ($this->fields as $fieldValue) {
            if (!$fieldValue->getIsValueSet()) {
                continue;
            }

            if ($fieldValue instanceof BooleanValue) {
                if (!$fieldValue->value_boolean) {
                    continue;
                }
            }

            $subQuery = $fieldValue->getRecordsQuery();
            $subQueryAlias = uniqid('t');

            $recordsQuery->innerJoin([$subQueryAlias => $subQuery],
                "[[{$recordsQuery->alias}]].[[id]] = [[$subQueryAlias]].[[registry_table_record_id]]");
        }

        return $recordsQuery;
    }

    public function formName()
    {
        return 's';
    }

    public function load($data, $formName = null)
    {
        if (!parent::load($data, $formName)) {
            return false;
        }

        $data = ArrayHelper::getValue($data, $this->formName());
        $data = ArrayHelper::getValue($data, 'f');

        if ($data !== null) {
            foreach ($data as $key => $value) {
                if (isset($this->fields[$key])) {
                    $this->fields[$key]->load($value, '');
                }
            }
        }

        return true;
    }
}
