<?php

/** @var \yii\web\View $this */
/** @var \Matodor\RegistryConstructor\models\forms\TableForm $tableForm */

$this->title = 'Создание таблицы';
$this->params['breadcrumbs'][] = ['label' => 'Конструктор таблиц', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="table-constructor-create">
    <?= $this->render('form/_form', ['tableForm' => $tableForm]) ?>
</div>
