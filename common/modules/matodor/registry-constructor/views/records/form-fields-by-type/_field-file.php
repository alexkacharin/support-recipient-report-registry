<?php

use kartik\file\FileInput;
use Matodor\Common\components\Helper;
use Matodor\RegistryConstructor\models\data\File\ValueForm;
use Matodor\RegistryConstructor\models\TableField;
use yii\helpers\Html;
use yii\web\View;
use yii\bootstrap4\ActiveField;
use yii\bootstrap4\ActiveForm;

/** @var View $this */
/** @var TableField $field */
/** @var ValueForm $fieldValue */
/** @var ActiveForm $form */
/** @var ActiveField $formField */

?>

<?= Html::error($fieldValue, 'uploadedFile') ?>

<?php if ($fieldValue->hasErrors('file_token')
    && $fieldValue->isAttributeRequired('file_token')
    && Helper::isEmpty($fieldValue->file_token)
) { ?>
    <div class="mb-2 alert alert-danger">Необходимо загрузить файл!</div>
<?php } ?>

<?php if ($fieldValue->getIsValueSet()) { ?>
    <div class="d-flex align-items-center">
        <span>Загруженный файл:</span>
        <span class="ml-2 badge badge-secondary">
            <?= $fieldValue->getFormattedValue() ?>
        </span>
    </div>
    <?= $form->field($fieldValue, 'removeFile')->checkbox(); ?>
<?php } ?>

<?= $form->field($fieldValue, 'uploadedFile', [
    'inputOptions' => [
        'class' => 'form-control',
        'placeholder' => $field->placeholder,
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
        'msgPlaceholder' => $field->placeholder,
    ],
])->label(false) ?>

