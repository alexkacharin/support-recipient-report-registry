<?php

/** @var \yii\web\View $this */
/** @var \Matodor\RegistryConstructor\models\forms\TableForm $tableForm */

$this->title = "Редактирование таблицы: #{$tableForm->id} {$tableForm->name}";
$this->params['breadcrumbs'][] = ['label' => 'Конструктор таблиц', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="table-constructor-edit">
    <?= $this->render('form/_form', ['tableForm' => $tableForm]) ?>
</div>
