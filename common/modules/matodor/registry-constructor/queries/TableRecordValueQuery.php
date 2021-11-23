<?php

namespace Matodor\RegistryConstructor\queries;

use Exception;
use Matodor\RegistryConstructor\components\ValueType;
use Matodor\RegistryConstructor\models\forms\TableFieldForm;
use Matodor\RegistryConstructor\models\TableField;
use Matodor\RegistryConstructor\models\TableRecord;
use Matodor\RegistryConstructor\models\TableRecordValue;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\NotFoundHttpException;

class TableRecordValueQuery extends ActiveQuery
{
    /**
     * @var bool
     */
    public $asForm = false;

    /**
     * @var TableField[]
     */
    protected $_tableFields = [];

    /**
     * Converts found rows into model instances.
     *
     * @param array $rows
     *
     * @return array|ActiveRecord[]
     * @throws InvalidConfigException
     * @throws NotFoundHttpException
     * @throws Exception
     * @since 2.0.11
     */
    protected function createModels($rows)
    {
        if ($this->asArray) {
            return $rows;
        } else {
            $models = [];

            /** @var TableRecord $primaryModel */
            $primaryModel = $this->primaryModel;

            foreach ($rows as $row) {
                if (!isset($row['registry_table_field_id'])) {
                    continue;
                }

                /** @var TableField $tableField */
                $tableField = null;

                if ($primaryModel instanceof TableRecord
                    && $primaryModel->isRelationPopulated('table')
                    && $primaryModel->table->isRelationPopulated('fields')
                ) {
                    $tableField = $primaryModel
                        ->table
                        ->getField($row['registry_table_field_id']);
                }

                if ($tableField === null) {
                    $tableField = $this->getTableField($row['registry_table_field_id']);
                }

                /** @var TableRecordValue $class */
                $class = ValueType::instance()->getRecordValueClass(
                    $tableField->type,
                    $tableField->value_type,
                    $this->asForm
                );

                /** @var TableRecordValue $model */
                $model = Yii::createObject([
                    'class' => $class,
                    'field' => $tableField,
                ]);

                $class::populateRecordFromUnion($model, $row);

                // set model field again,
                // because it's resetting after populateRecord
                $model->setField($tableField);

                if ($primaryModel instanceof TableRecord) {
                    $model->populateRelation('record', $primaryModel);
                }

                $models[] = $model;
            }

            return $models;
        }
    }

    /**
     * @param integer|string $id
     *
     * @return TableField
     * @throws InvalidConfigException
     * @throws NotFoundHttpException
     */
    protected function getTableField($id)
    {
        if (!isset($this->_tableFields[$id])) {
            $this->_tableFields[$id] = $this->asForm
                ? TableField::findModel($id)
                : TableFieldForm::findModel($id);
        }

        return $this->_tableFields[$id];
    }
}
