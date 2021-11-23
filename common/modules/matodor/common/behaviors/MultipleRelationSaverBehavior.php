<?php

namespace Matodor\Common\behaviors;

use Closure;
use Exception;
use Matodor\Common\components\Helper;
use Matodor\Common\models\BaseModel;
use Matodor\RegistryConstructor\events\LoadEvent;
use Yii;
use yii\base\Behavior;
use yii\base\Event;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\db\AfterSaveEvent;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class MultipleRelationSaverBehavior extends Behavior
{
    /**
     * @var array
     */
    public $processFields = [];

    public function events()
    {
        $events = parent::events();
        $events[BaseModel::EVENT_AFTER_LOAD] = 'loadFields';
        $events[BaseModel::EVENT_AFTER_VALIDATE] = 'validateFields';
        $events[BaseModel::EVENT_AFTER_UPDATE] = 'saveFields';
        $events[BaseModel::EVENT_AFTER_INSERT] = 'saveFields';

        return $events;
    }

    /**
     * @param AfterSaveEvent $event
     *
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     * @noinspection PhpUnused
     */
    public function saveFields(AfterSaveEvent $event)
    {
        foreach ($this->processFields as $item) {
            /** @var BaseModel $parent */
            $parent = $this->owner;
            /** @var string $attribute */
            $attribute = $item['attribute'];

            /** @var string $foreignKey */
            $foreignKey = ArrayHelper::getValue($item, 'foreignKey', false);
            /** @var Closure|null $beforeSave */
            $beforeSave = ArrayHelper::getValue($item, 'beforeSave');

            /** @var BaseModel[] $models */
            $models = $parent->{$attribute};
            $keepKeys = [];
            $index = 0;

            foreach ($models as $key => $model) {
                $model->setAttribute($foreignKey, $parent->primaryKey);

                if ($beforeSave instanceof Closure) {
                    if ($beforeSave($model, $index++, $key, $parent) === false) {
                        continue;
                    }
                }

                if ($model->save(false)) {
                    $keepKeys[] = $model->primaryKey;
                }
            }

            if (ArrayHelper::getValue($item, 'deleteNotProvided', true) === true) {
                /** @var string|BaseModel $class */
                $class = $item['class'];

                $deleteCondition = [
                    'AND',
                    ['=', $foreignKey, $parent->primaryKey],
                    ['not in', 'id', $keepKeys],
                ];

                foreach ($class::find()
                    ->where($deleteCondition)
                    ->all() as $model
                ) {
                    $model->delete();
                }
            }
        }
    }

    /**
     * @param Event $event
     *
     * @noinspection PhpUnused
     * @throws Exception
     */
    public function validateFields(Event $event)
    {
        foreach ($this->processFields as $item) {
            /** @var BaseModel $parent */
            $parent = $this->owner;
            /** @var string $attribute */
            $attribute = $item['attribute'];
            /** @var string $foreignKey */
            $foreignKey = ArrayHelper::getValue($item, 'foreignKey', false);
            $valid = true;

            foreach ($parent->{$attribute} as $model) {
                /* @var $model Model */

                $attributeNames = array_diff($model->attributes(), [
                    $foreignKey,
                ]);

                $valid = $model->validate($attributeNames, false) && $valid;
            }

            if (!$valid) {
                $parent->addError($attribute, $parent->getAttributeLabel($attribute) . ' - Исправьте ошибки при заполнении');
            }
        }
    }

    /**
     * @param LoadEvent $event
     *
     * @throws InvalidConfigException
     * @throws NotFoundHttpException
     * @throws Exception
     * @noinspection PhpUnused
     */
    public function loadFields(LoadEvent $event)
    {
        /** @var BaseModel $parent */
        $parent = $this->owner;

        foreach ($this->processFields as $item) {
            /** @var string $attribute */
            $attribute = $item['attribute'];

            if (!$parent->isAttributeSafe($attribute)) {
                continue;
            }

            if (ArrayHelper::getValue($item, 'loadFields', true) === false) {
                continue;
            }

            /** @var string|BaseModel|Closure $class */
            $class = $item['class'];

            /** @var string $foreignKey */
            $foreignKey = ArrayHelper::getValue($item, 'foreignKey', false);

            /** @var Closure|null $modelsFactory */
            $modelsFactory = ArrayHelper::getValue($item, 'modelsFactory');

            /** @var Closure|null $modelsFactory */
            $modelFactory = ArrayHelper::getValue($item, 'modelFactory');

            if (!is_array($parent->{$attribute})
                || count($parent->{$attribute}) === 0
            ) {
                continue;
            }

            /** @var BaseModel[] $models */
            $models = [];
            $index = 0;

            if ($modelsFactory instanceof Closure) {
                $models = $modelsFactory($parent->{$attribute}, $parent);
            } else {
                foreach ($parent->{$attribute} as $key => $itemData) {
                    /** @var array $itemData */

                    if ($class instanceof Closure) {
                        $class = $class($itemData, $index++, $key, $parent);
                    }

                    $itemId = ArrayHelper::getValue($itemData, $class::primaryKey());
                    $model = null;

                    if ($modelFactory instanceof Closure) {
                        $model = $modelFactory($itemData, $itemId, $index++, $key, $parent);
                    } else {
                        if (!$parent->isNewRecord
                            && !Helper::isEmpty($itemId)
                        ) {
                            $model = $class::findModel($itemId, false);

                            if ($foreignKey
                                && $model !== null
                                && $model->getAttribute($foreignKey) !== $parent->primaryKey
                            ) {
                                $model = null;
                            }
                        }
                    }

                    $models[$key] = $model ?? Yii::createObject($class);
                }
            }

            $event->isValid = Model::loadMultiple($models, $parent->{$attribute}, '')
                && $event->isValid;

            if ($foreignKey) {
                foreach ($models as $key => $model) {
                    if ($model->isNewRecord) {
                        continue;
                    }

                    if ($parent->isNewRecord
                        || $parent->primaryKey != $model->getAttribute($foreignKey)
                    ) {
                        unset($models[$key]);
                    }
                }
            }

            if ($event->isValid) {
                $parent->{$attribute} = $models;
            }
        }
    }
}
