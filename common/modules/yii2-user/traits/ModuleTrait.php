<?php

namespace dektrium\user\traits;

use dektrium\user\Module;
use Yii;

/**
 * Trait ModuleTrait
 *
 * @property-read Module $module
 * @package dektrium\user\traits
 */
trait ModuleTrait
{
    /**
     * @return Module|\yii\base\Module
     * @noinspection PhpReturnDocTypeMismatchInspection
     */
    public function getModule()
    {
        return Yii::$app->getModule('user');
    }

    /**
     * @return string
     */
    public static function getDb()
    {
        return Yii::$app->getModule('user')->getDb();
    }
}
