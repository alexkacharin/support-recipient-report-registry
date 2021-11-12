<?php

use kartik\select2\Select2;
use Matodor\RegistryConstructor\models\data\Date\Settings;
use Matodor\RegistryConstructor\models\forms\TableFieldForm;
use yii\bootstrap4\ActiveForm;
use yii\web\View;

/** @var View $this */
/** @var TableFieldForm $tableFieldForm */
/** @var ActiveForm $form */
/** @var Settings $settings */

?>

<div class="row mx-n1">
    <div class="col-12 col-md-12 px-1">
        <?= $form->field($settings, 'format', [
            'labelOptions' => [
                'class' => 'control-label',
            ],
            'options' => [
                'class' => 'form-group mb-0',
            ],
        ])->widget(Select2::class, [
            'data' => $settings->getFormatsSelectData(),
            'pluginLoading' => false,
            'theme' => Select2::THEME_BOOTSTRAP,
            'options' => [
                'id' => uniqid('id'),
                'placeholder' => $settings->getAttributeLabel('format'),
            ],
        ]) ?>

        <?= $form->field($settings, 'template')->textarea([
            'id' => uniqid('id'),
            'placeholder' => 'Пример: ${<Value>}',
        ]); ?>

        <?= $form->field($settings, 'template_in_table')->textarea([
            'id' => uniqid('id'),
            'placeholder' => 'Пример: ${<FormattedValue>}',
        ]); ?>
    </div>
</div>
