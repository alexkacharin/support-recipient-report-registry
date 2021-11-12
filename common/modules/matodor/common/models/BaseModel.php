<?php

namespace Matodor\Common\models;

use Carbon\Carbon;
use Exception;
use Matodor\Common\behaviors\AttributeTypecastBehavior;
use Matodor\Common\components\Helper;
use Matodor\RegistryConstructor\events\LoadEvent;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\ModelEvent;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQueryInterface;
use yii\db\ActiveRecord;
use yii\db\ActiveRecordInterface;
use yii\web\NotFoundHttpException;

/**
 * Базовый класс для всех моделей в системе
 *
 * @property-read int|void $updated_at
 * @property-read int|void $created_at
 * @property-read string $formattedCreatedAt
 * @property-read string $formattedUpdatedAt
 *
 * @mixin AttributeTypecastBehavior
 * @mixin TimestampBehavior
 */
abstract class BaseModel extends ActiveRecord
{
    public const SCENARIO_SEARCH = 'search';
    public const EVENT_BEFORE_LOAD = 'beforeLoad';
    public const EVENT_AFTER_LOAD = 'afterLoad';

    public $attachTimestamps = true;

    /**
     * @return array
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['typecast'] = [
            'class' => AttributeTypecastBehavior::class,
        ];

        if ($this->attachTimestamps) {
            $behaviors['timestamp'] = [
                'class' => TimestampBehavior::class,
                'skipUpdateOnClean' => false,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
            ];
        }

        return $behaviors;
    }

    /**
     * @throws InvalidConfigException
     */
    public function load($data, $formName = null)
    {
        if (empty($data)) {
            return false;
        }

        $scope = $formName === null
            ? $this->formName()
            : $formName;

        if ($scope !== '') {
            if (isset($data[$scope])) {
                $data = $data[$scope];
            } else {
                return false;
            }
        }

        $beforeEvent = new LoadEvent();
        $beforeEvent->loadData = $data;
        $this->trigger(static::EVENT_BEFORE_LOAD, $beforeEvent);

        if (!$beforeEvent->isValid
            || !parent::load($data, '')
        ) {
            return false;
        }

        $afterEvent = new LoadEvent();
        $afterEvent->loadData = $data;
        $this->trigger(static::EVENT_AFTER_LOAD, $afterEvent);

        return $afterEvent->isValid;
    }

    /**
     * @param integer|string|array $id
     * @param bool $throw
     *
     * @return null|static|array|BaseModel|ActiveRecordInterface
     * @throws NotFoundHttpException
     * @throws InvalidConfigException
     */
    public static function findModel($id, bool $throw = true)
    {
        if (Helper::isEmpty($id)) {
            /** @noinspection PhpUnhandledExceptionInspection */
            throw new NotFoundHttpException('Запрашиваемая страница не найдена');
        }

        /** @noinspection PhpInternalEntityUsedInspection */
        /** @noinspection PhpUnhandledExceptionInspection */
        $model = static::findByCondition($id)
            ->limit(1)
            ->one();

        if ($model !== null) {
            return $model;
        }

        if ($throw) {
            /** @noinspection PhpUnhandledExceptionInspection */
            throw new NotFoundHttpException('Запрашиваемая страница не найдена');
        } else {
            return null;
        }
    }

    /**
     * @param string $class the class name of the related record
     * @param array $link the primary-foreign key constraint. The keys of the array refer to
     * the attributes of the record associated with the `$class` model, while the values of the
     * array refer to the corresponding attributes in **this** AR class.
     *
     * @return ActiveQueryInterface the relational query object.
     */
    public function hasOne($class, $link)
    {
        return parent::hasOne($class, $link)->limit(1);
    }

    /**
     * @param bool $withTime
     *
     * @return string
     * @throws Exception
     * @noinspection PhpUnused
     */
    public function getFormattedUpdatedAt(bool $withTime = true)
    {
        if (!$this->canGetProperty('updated_at')) {
            throw new Exception('Property `updated_at` not exist');
        }


        return Carbon::createFromTimestampUTC($this->updated_at)
            ->format($withTime ? 'd.m.Y H:i' : 'd.m.Y');
    }

    /**
     * @param bool $withTime
     *
     * @return string
     * @throws Exception
     * @noinspection PhpUnused
     */
    public function getFormattedCreatedAt(bool $withTime = true)
    {
        if (!$this->canGetProperty('created_at')) {
            throw new Exception('Property `created_at` not exist');
        }

        return Carbon::createFromTimestampUTC($this->created_at)
            ->format($withTime ? 'd.m.Y H:i' : 'd.m.Y');
    }

    /**
     * @noinspection TranslationsCorrectnessInspection
     */
    public function getAttributeLabel($attribute)
    {
        if ($attribute === 'created_at') {
            return Yii::t('yii', 'Время создания');
        } else if ($attribute === 'updated_at') {
            return Yii::t('yii', 'Время обновления');
        }

        return parent::getAttributeLabel($attribute);
    }

    /**
     * @param string $name
     * @param mixed $records
     *
     * @return static
     */
    public function populateRelationIfNeeded(string $name, $records)
    {
        if ($this->isRelationPopulated($name) === false) {
            $this->populateRelation($name, $records);
        }

        return $this;
    }
}
