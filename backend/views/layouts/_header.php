<?php

use Matodor\Common\components\Helper;
use Matodor\RegistryConstructor\models\Table;
use Matodor\RegistryConstructor\Module as RegistryModule;
use yii\bootstrap4\Nav;
use yii\bootstrap4\NavBar;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/** @var \yii\web\View $this */

$menuItems = [];
$registriesItems = ArrayHelper::getColumn(
    Table::find()
        ->select(['name', 'id'])
        ->isVisibleInMenu()
        ->isType(Table::TABLE_TYPE_MAIN)
        ->andWhereHasAnyPermission()
        ->asArray()
        ->all(),
    function ($item) {
        return [
            'label' => $item['name'],
            'url' => RegistryModule::getInstance()->toRoute([
                'records/index',
                'tableId' => $item['id'],
            ]),
        ];
    }
);

if (Yii::$app->user->isGuest) {
    $menuItems[] = [
        'label' => 'Вход',
        'url' => ['/user/security/login'],
        'linkOptions' => ['class' => 'header__navbar-link'],
    ];
} else {
    $menuItems[] = [
        'label' => '<i class="fas fa-external-link-alt"></i>',
        'url' => Helper::to(['/site/index'], 'frontend', true),
        'linkOptions' => ['class' => 'header__navbar-link', 'target' => '_blank',],
        'encode' => false,
    ];

    $menuItems[] = [
        'label' => '<i class="fas fa-home"></i> Главная',
        'url' => ['/site/index'],
        'linkOptions' => ['class' => 'header__navbar-link'],
        'encode' => false,
    ];

    $menuItems[] = [
        'label' => '<i class="fas fa-book"></i> Реестры',
        'linkOptions' => ['class' => 'header__navbar-link'],
        'items' => $registriesItems,
        'encode' => false,
    ];

    if (Yii::$app->user->can(RegistryModule::CAN_VIEW_CONSTRUCTOR_TABLES_LIST)) {
        $menuItems[] = [
            'label' => '<i class="fas fa-pencil-alt"></i> Конструктор',
            'url' => RegistryModule::getInstance()->toRoute(['table-constructor/index']),
            'linkOptions' => ['class' => 'header__navbar-link'],
            'encode' => false,
        ];
    }

    // Настройки
    {
        $items = [];
        $items[] = ['label' => 'Пользователи', 'url' => ['/user/admin/index']];
        $items[] = ['label' => 'Роли', 'url' => ['/rbac/role/index']];
        $items[] = ['label' => 'Разрешения', 'url' => ['/rbac/permission/index']];
        $items[] = ['label' => 'Главная страница', 'url' => ['/frontend-settings/index']];

        $menuItems[] = [
            'label' => '<i class="fas fa-cog"></i> Настройки',
            'linkOptions' => ['class' => 'header__navbar-link'],
            'items' => $items,
            'encode' => false,
        ];
    }
}

?>

<div id="header" class="header" style="min-height: 43px">
    <?php NavBar::begin([
        'id' => 'navbar-top',
        'brandLabel' => '<img class="header__navbar-logo-img" src="/images/flag_mini.png">',
        'brandUrl' => Yii::$app->homeUrl,
        'brandOptions' => [
            'class' => 'header__navbar-link',
        ],
        'renderInnerContainer' => false,
        'togglerContent' => '<span class="navbar-toggler-icon"><i class="fa fa-bars"></i></span>',
        'options' => [
            'class' => 'header__navbar fixed-top navbar-expand-lg py-0',
        ],
    ]); ?>
        <?= Nav::widget([
            'id' => 'navbar-nav-top',
            'options' => ['class' => 'navbar-nav mr-auto'],
            'items' => $menuItems,
        ]) ?>

        <?php if (!Yii::$app->user->isGuest) { ?>
            <div class="d-flex align-items-center">
                <div class="badge badge-light"><?= Yii::$app->user->identity->username ?></div>
                <div class="ml-2">
                    <?= Html::beginForm(['/user/security/logout'], 'post', ['class' => 'form-inline my-2 my-lg-0']) ?>
                    <?= Html::submitButton('Выход', ['class' => 'btn btn-sm btn-dark my-2 px-4 my-sm-0 logout']) ?>
                    <?= Html::endForm() ?>
                </div>
            </div>
        <?php } ?>
    <?php NavBar::end(); ?>
</div>
