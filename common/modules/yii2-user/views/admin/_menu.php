<?php

use yii\bootstrap4\Nav;
use yii\web\View;

/** @var View $this */

$rbacModuleClass = 'dektrium\rbac\RbacWebModule';
$isRbacModuleInstalled = Yii::$app->getModule('rbac') instanceof $rbacModuleClass;

?>

<div class="block-box border-default py-0 mb-4">
    <div class="block-box__body block-box__body_no-padding position-relative">
        <?= Nav::widget([
            'options' => [
                'class' => 'nav-tabs nav-pills',
            ],
            'items' => [
                [
                    'label' => Yii::t('user', 'Users'),
                    'url' => ['/user/admin/index'],
                ],
                [
                    'label' => Yii::t('user', 'Roles'),
                    'url' => ['/rbac/role/index'],
                    'visible' => $isRbacModuleInstalled,
                ],
                [
                    'label' => Yii::t('user', 'Permissions'),
                    'url' => ['/rbac/permission/index'],
                    'visible' => $isRbacModuleInstalled,
                ],
                [
                    'label' => \Yii::t('user', 'Rules'),
                    'url'   => ['/rbac/rule/index'],
                    'visible' => $isRbacModuleInstalled,
                ],
                [
                    'label' => Yii::t('user', 'Create'),
                    'items' => [
                        [
                            'label' => Yii::t('user', 'New user'),
                            'url' => ['/user/admin/create'],
                        ],
                        [
                            'label' => Yii::t('user', 'New role'),
                            'url' => ['/rbac/role/create'],
                            'visible' => $isRbacModuleInstalled,
                        ],
                        [
                            'label' => Yii::t('user', 'New permission'),
                            'url' => ['/rbac/permission/create'],
                            'visible' => $isRbacModuleInstalled,
                        ],
                        [
                            'label' => \Yii::t('user', 'New rule'),
                            'url'   => ['/rbac/rule/create'],
                            'visible' => $isRbacModuleInstalled,
                        ],
                    ],
                ],
            ],
        ]) ?>
    </div>
</div>
