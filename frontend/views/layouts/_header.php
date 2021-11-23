<?php

use yii\bootstrap4\Nav;
use yii\bootstrap4\NavBar;
use yii\helpers\Html;
use yii\web\View;

/** @var View $this */

$menuItems = [];

if (Yii::$app->user->isGuest) {
    $menuItems[] = [
        'label' => 'Вход',
        'url' => ['/user/security/login'],
        'linkOptions' => ['class' => 'header__navbar-link'],
    ];
} else {
    $menuItems[] = [
        'label' => '<i class="fas fa-home"></i> Главная',
        'url' => ['/site/index'],
        'linkOptions' => ['class' => 'header__navbar-link'],
        'encode' => false,
    ];
}

?>

<div id="header" class="header" style="min-height: 59px;">
    <?php NavBar::begin([
            'brandLabel' => '<img class="header__navbar-logo-img" src="/images/flag_mini.png">',
            'brandUrl' => Yii::$app->homeUrl,
            'brandOptions' => [
                'class' => 'header__navbar-link',
            ],
            'togglerContent' => '<span class="navbar-toggler-icon"><i class="fa fa-bars"></i></span>',
            'id' => 'navbar-top',
            'options' => [
                'class' => 'header__navbar fixed-top navbar-expand-lg',
            ],
        ]);
    ?>
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
                        <?= Html::submitButton('Выход', ['class' => 'btn btn-light my-2 px-4 my-sm-0 logout']) ?>
                    <?= Html::endForm() ?>
                </div>
            </div>
        <?php } ?>
    <?php NavBar::end(); ?>
</div>
