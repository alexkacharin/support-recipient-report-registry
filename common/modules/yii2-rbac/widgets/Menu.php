<?php

namespace dektrium\rbac\widgets;

use Yii;
use yii\bootstrap4\Nav;
use yii\helpers\Html;

/**
 * Menu widget.
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class Menu extends Nav
{
    /**
     * @inheritdoc
     */
    public $options = [
        'class' => 'nav-tabs'
    ];

    /**
     * @inheritdoc
     */
    public function init()
    {
        Html::addCssClass($this->options, 'nav-tabs nav-pills');

        parent::init();

        $userModuleClass = 'dektrium\user\Module';
        $isUserModuleInstalled = Yii::$app->getModule('user') instanceof $userModuleClass;

        $this->items = [
            [
                'label'   => Yii::t('rbac', 'Users'),
                'url'     => ['/user/admin/index'],
                'visible' => $isUserModuleInstalled,
            ],
            [
                'label' => Yii::t('rbac', 'Roles'),
                'url'   => ['/rbac/role/index'],
            ],
            [
                'label' => Yii::t('rbac', 'Permissions'),
                'url'   => ['/rbac/permission/index'],
            ],
            [
                'label' => Yii::t('rbac', 'Rules'),
                'url'   => ['/rbac/rule/index'],
            ],
            [
                'label' => Yii::t('rbac', 'Create'),
                'items' => [
                    [
                        'label'   => Yii::t('rbac', 'New user'),
                        'url'     => ['/user/admin/create'],
                        'visible' => $isUserModuleInstalled,
                    ],
                    [
                        'label' => Yii::t('rbac', 'New role'),
                        'url'   => ['/rbac/role/create']
                    ],
                    [
                        'label' => Yii::t('rbac', 'New permission'),
                        'url'   => ['/rbac/permission/create']
                    ],
                    [
                        'label' => Yii::t('rbac', 'New rule'),
                        'url'   => ['/rbac/rule/create']
                    ],
                ],
            ],
        ];
    }
}
