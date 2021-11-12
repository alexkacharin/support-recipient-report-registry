<?php

namespace dektrium\rbac\controllers;

use yii\rbac\Permission;
use yii\web\NotFoundHttpException;
use yii\rbac\Item;

class PermissionController extends ItemControllerAbstract
{
    /**
     * @var string
     */
    protected $modelClass = \dektrium\rbac\models\Permission::class;

    /**
     * @var int
     */
    protected $type = Item::TYPE_PERMISSION;

    /**
     * @inheritdoc
     */
    protected function getItem($name)
    {
        $role = \Yii::$app->authManager->getPermission($name);

        if ($role instanceof Permission) {
            return $role;
        }

        throw new NotFoundHttpException;
    }
}
