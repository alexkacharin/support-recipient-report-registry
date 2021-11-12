<?php

namespace dektrium\rbac;

use yii\base\Module as BaseModule;
use yii\filters\AccessControl;

/**
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class RbacWebModule extends BaseModule
{
    /**
     * @var string
     */
    public $defaultRoute = 'role/index';

    /**
     * @var array
     */
    public $admins = [];

	/**
     * @var string The Administrator permission name.
     */
    public $adminPermission;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow'         => true,
                        'roles'         => ['@'],
                        'matchCallback' => [$this, 'checkAccess'],
                    ]
                ],
            ],
        ];
    }

    /**
     * Checks access.
     *
     * @return bool
     */
    public function checkAccess()
    {
        $user = \Yii::$app->user->identity;

        if (method_exists($user, 'getIsAdmin')) {
            return $user->getIsAdmin();
        } else if ($this->adminPermission) {
            return \Yii::$app->user->can($this->adminPermission);
        } else {
            return isset($user->username) ? in_array($user->username, $this->admins) : false;
        }
    }
}
