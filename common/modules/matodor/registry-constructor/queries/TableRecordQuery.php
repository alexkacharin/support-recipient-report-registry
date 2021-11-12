<?php

namespace Matodor\RegistryConstructor\queries;

use Matodor\Common\models\BaseModel;
use Matodor\RegistryConstructor\models\Table;
use Matodor\RegistryConstructor\models\TablePermissions;
use Yii;
use yii\base\Exception;
use yii\base\InvalidArgumentException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

class TableRecordQuery extends ActiveQuery
{
    /**
     * @var string|null Alias for records table
     */
    public $alias = 'a_records';

    public function init()
    {
        parent::init();

        $this->alias($this->alias);
    }

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

    /**
     * @param int|string|null $userId
     *
     * @return TableRecordQuery
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function andWhereHasViewPermissions($userId = null)
    {
        if ($userId === null) {
            $userId = Yii::$app->user->id;
        }

        if (!$this->primaryModel instanceof Table) {
            throw new Exception('`primaryModel` not instance of Table model');
        }

        /** @var Table $table */
        $table = $this->primaryModel;

        if ($table->checkAccess($userId, TablePermissions::CAN_VIEW_ALL_RECORDS)) {
            return $this;
        }

        if ($userId !== null
            && $table->checkAccess($userId, TablePermissions::CAN_VIEW_SELF_RECORDS)
        ) {
            return $this->andWhere(['user_created_id' => $userId]);
        }

        return $this->emulateExecution();
    }
}
