<?php

use kartik\file\FileInput;
use Matodor\RegistryConstructor\models\forms\TableRecordsImportForm;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/** @var View $this */
/** @var TableRecordsImportForm $importForm */

?>

<div class="registry-table-record__import-form">
    <?php $form = ActiveForm::begin([
        'action' => Url::toRoute([
            'records/import',
            'tableId' => $importForm->table->id,
        ]),
        'id' => uniqid('f'),
        'options' => ['class' => 'd-flex flex-column form'],
    ]); ?>
        <?= Html::errorSummary($importForm) ?>

        <?= $form->field($importForm, 'uploadedFile', [
            'inputOptions' => [
                'class' => 'form-control',
                'placeholder' => 'Файл для импорта не выбран',
                'id' => uniqid('f'),
            ],
            'labelOptions' => [
                'class' => 'control-label',
            ],
            'options' => [
                'class' => 'form-group mb-0',
            ],
        ])->widget(FileInput::class, [
            'pluginOptions' => [
                'showPreview' => false,
                'showCaption' => true,
                'showRemove' => true,
                'showUpload' => false,
                'msgPlaceholder' => 'Файл для импорта не выбран',
            ],
        ]) ?>

        <div class="mt-2">
            <div class="d-flex m-n1">
                <?= Html::a('<i class="fas fa-file-download"></i> Скачать бланк', [
                    'import-example',
                    'tableId' => $importForm->table->id,
                ], [
                    'class' => 'btn flex-grow-1 btn-info m-1',
                ]) ?>

                <?= Html::submitButton('Импортировать', [
                    'class' => 'btn flex-grow-1 btn-success m-1',
                ]) ?>
            </div>
        </div>
    <?php ActiveForm::end(); ?>
</div>

