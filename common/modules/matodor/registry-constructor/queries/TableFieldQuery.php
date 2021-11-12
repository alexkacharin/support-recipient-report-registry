<?php

namespace Matodor\RegistryConstructor\queries;

use Matodor\Common\models\BaseModel;
use Matodor\RegistryConstructor\models\Table;
use yii\db\ActiveRecord;

class TableFieldQuery extends \yii\db\ActiveQuery
{
    /**
     * @noinspection DuplicatedCode
     */
    protected function createModels($rows)
    {
        if ($this->asArray) {
            return $rows;
        } else {
            $models = [];

            /* @var $class BaseModel */
            $class = $this->modelClass;

            foreach ($rows as $row) {
                $model = $class::instantiate($row);
                /** @var ActiveRecord $modelClass */
                $modelClass = get_class($model);
                $modelClass::populateRecord($model, $row);

                if ($this->primaryModel instanceof Table) {
                    $model->populateRelationIfNeeded('table', $this->primaryModel);
                }

                $models[] = $model;
            }

            return $models;
        }
    }
}
