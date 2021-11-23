<?php

/* @var $this yii\web\View */

use Matodor\Common\widgets\HtmlBlock\DisplayWidget as DisplayHtmlWidget;
use Matodor\RegistryConstructor\Module as RegistryModule;
use Matodor\RegistryConstructor\models\Table;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

$this->title = DisplayHtmlWidget::encodedValue('frontend_title', 'Заголовок страницы');
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

?>

<div class="site-index">
    <div class="jumbotron">
        <h1>
            <?= DisplayHtmlWidget::widget([
                'key' => 'frontend_header_1',
                'defaultContent' => 'Добро пожаловать!',
                'containerOptions' => [
                    'class' => 'h1',
                ],
            ]) ?>
        </h1>

        <p class="lead">
            <?= DisplayHtmlWidget::widget([
                'key' => 'frontend_subheader_1',
                'defaultContent' => 'Подзаголовок',
                'containerOptions' => [
                    'class' => 'h1',
                ],
            ]) ?>
        </p>

        <?= DisplayHtmlWidget::widget([
            'key' => 'frontend_description',
            'defaultContent' => 'Описание',
        ]) ?>
    </div>

    <div>
        <?php foreach ($registriesItems as $menuItem) { ?>
            <div class="mt-2">
                <?= Html::a($menuItem['label'], $menuItem['url'], [
                    'class' => 'btn btn-success',
                ]) ?>
            </div>
        <?php } ?>
    </div>
</div>
