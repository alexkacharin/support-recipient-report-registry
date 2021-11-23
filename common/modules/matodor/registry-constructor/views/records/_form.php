<?php

use Matodor\RegistryConstructor\assets\RecordCrudAssets;
use Matodor\RegistryConstructor\models\forms\TableRecordForm;
use Matodor\RegistryConstructor\models\Table;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/** @var View $this */
/** @var TableRecordForm $tableRecord */
/** @var Table $table */

RecordCrudAssets::register($this);

foreach ($tableRecord->table->records_assets as $class) {
    $this->registerAssetBundle($class);
}

?>

<div class="registry-table-record__form">
    <?php $form = ActiveForm::begin([
        'id' => uniqid('f'),
        'options' => ['class' => 'd-flex flex-column form w-100'],
    ]); ?>
        <?= Html::errorSummary($tableRecord) ?>
        <?= print_r($tableRecord->getErrors()) ?>

        <div class="block-box border-default">
            <div class="block-box__body d-flex flex-column">
                <div class="mt-n2">
                    <?= $this->render('_fields', [
                        'table' => $table,
                        'tableRecord' => $tableRecord,
                        'form' => $form,
                    ]) ?>
                </div>

                <div class="mt-2">
                    <?= Html::submitButton($tableRecord->isNewRecord ? 'Сохранить' : 'Обновить', [
                        'class' => 'btn btn-success',
                    ]) ?>
                </div>
            </div>
        </div>
    <?php ActiveForm::end(); ?>
</div>

