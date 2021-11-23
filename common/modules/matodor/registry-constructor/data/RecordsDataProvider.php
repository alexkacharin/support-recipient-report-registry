<?php

namespace Matodor\RegistryConstructor\data;

use Matodor\RegistryConstructor\models\Table;
use Matodor\RegistryConstructor\models\TableField;
use Matodor\RegistryConstructor\queries\TableRecordQuery;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\db\QueryInterface;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;

class RecordsDataProvider extends ActiveDataProvider
{
    /**
     * @var TableRecordQuery
     */
    public $query = null;

    /**
     * @var Table
     */
    public $table = null;

    public function __construct($config = [])
    {
        $this->table = ArrayHelper::remove($config, 'table');
        $attributes = ArrayHelper::remove($config, 'sort.attributes', []);

        if (empty($attributes)) {
            $attributes = $this->getDefaultSortAttributes();
            ArrayHelper::setValue($config, 'sort.attributes', $attributes);
        }

        parent::__construct($config);
    }

    /**
     * @return string[]|mixed
     */
    public function getDefaultSortAttributes()
    {
        foreach ([
            'id',
            'created_at',
            'updated_at',
        ] as $attribute) {
            $attributes[$attribute] = [
                'asc' => [$attribute => SORT_ASC],
                'desc' => [$attribute => SORT_DESC],
                'default' => SORT_DESC,
            ];
        }

        foreach ($this->table->fields as $field) {
            if ($field->type !== TableField::FIELD_TYPE_INPUT) {
                continue;
            }

            $attr = "f_{$field->id}";
            $attributes[$attr] = [
                'asc' => [$attr => SORT_ASC],
                'desc' => [$attr => SORT_DESC],
                'default' => SORT_DESC,
                'label' => $field->name,
            ];
        }

        return $attributes;
    }

    protected function prepareModels()
    {
        if (!$this->query instanceof QueryInterface) {
            throw new InvalidConfigException('The "query" property must be an instance of a class that implements the QueryInterface e.g. yii\db\Query or its subclasses.');
        }

        $query = clone $this->query;
        $pagination = null;
        $sort = null;

        if (($pagination = $this->getPagination()) !== false) {
            $pagination->totalCount = $this->getTotalCount();

            if ($pagination->totalCount === 0) {
                return [];
            }

            $query->limit($pagination->getLimit())->offset($pagination->getOffset());
        }

        if (($sort = $this->getSort()) !== false) {
            $this->processOrders($query, $sort->getOrders());
        }

        return $query->all($this->db);
    }

    /**
     * @param TableRecordQuery $recordsQuery
     * @param mixed $orders
     *
     * @throws Exception
     */
    protected function processOrders(TableRecordQuery $recordsQuery, $orders)
    {
        if (is_array($orders)) {
            foreach ($orders as $attribute => $sort) {
                if (strncmp($attribute, 'f_', 2) === 0) {
                    $fieldId = (int) substr($attribute, 2);
                    $field = $this->table->getField($fieldId);

                    if ($field === null) {
                        continue;
                    }

                    $field
                        ->getValueTypeDefinition()
                        ->valueClass::setupRecordsQueryOrder($recordsQuery, $field->id, $sort);
                } else {
                    $recordsQuery->addOrderBy([$attribute => $sort]);
                }
            }
        } else {
            $recordsQuery->addOrderBy($orders);
        }
    }
}
