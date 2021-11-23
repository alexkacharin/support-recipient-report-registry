<?php

/** @var \yii\web\View $this */
/** @var \Matodor\RegistryConstructor\models\forms\TableRecordForm $tableRecord */
/** @var \Matodor\RegistryConstructor\models\Table $table */

$this->title = "Редактирование записи: #{$tableRecord->id}";
$this->params['breadcrumbs'][] = ['label' => $table->name, 'url' => ['index', 'tableId' => $table->id]];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="table-constructor-records-edit">
    <?= $this->render('_form', [
        'tableRecord' => $tableRecord,
        'table' => $table,
    ]) ?>
</div>
