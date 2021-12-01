<?php

namespace common\models;

use common\queries\UserQuery;
use dektrium\user\models\Profile;
use Yii;
use yii\db\ActiveQuery;

/**
 * Сущность пользователя в системе
 */
class User extends \dektrium\user\models\User
{
    private $_access = [];
    /**
     * @return UserQuery|ActiveQuery
     * @noinspection PhpUnhandledExceptionInspection
     * @noinspection PhpDocMissingThrowsInspection
     */
    public static function find()
    {
        return Yii::createObject(UserQuery::class, [get_called_class()]);
    }

    public function can($permissionName, $params = [], $allowCaching = true)
    {
        if ($allowCaching && empty($params) && isset($this->_access[$permissionName])) {
            return $this->_access[$permissionName];
        }

        if (($accessChecker = Yii::$app->authManager) === null) {
            return false;
        }

        $access = $accessChecker->checkAccess($this->getId(), $permissionName, $params);

        if ($allowCaching && empty($params)) {
            $this->_access[$permissionName] = $access;
        }

        return $access;
    }
   /* public function rules()
    {
                return [
                        ['status','default', 'value' => self::STATUS_INACTIVE],
                        ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE, self::STATUS_DELETED]],
                        [['inn'],'string','max'=>13],
                ];
    }
    public function attributeLabels()
    {
        $labels = parent::attributeLabels();
        $labels['captcha'] = 'Проверочный код';
        $labels['inn'] = 'ИНН';
        return $labels;
    }*/
}
