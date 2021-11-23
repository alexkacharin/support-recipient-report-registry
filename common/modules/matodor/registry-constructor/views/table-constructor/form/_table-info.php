<?php

use kartik\select2\Select2;
use Matodor\RegistryConstructor\models\forms\TableForm;
use yii\bootstrap4\ActiveForm;
use yii\web\View;

/** @var View $this */
/** @var TableForm $tableForm */
/** @var ActiveForm $form */

?>

<div class="row">
    <div class="col-md-6">
        <?= $form->field($tableForm, 'name', [
            'options' => [
                'class' => 'form-group'
            ]
        ])->textInput() ?>
    </div>

    <div class="col-md-6">
        <?= $form->field($tableForm, 'type')->widget(Select2::class, [
            'data' => TableForm::getTypeHeaders(),
            'pluginLoading' => false,
            'theme' => Select2::THEME_BOOTSTRAP,
            'options' => [
                'id' => uniqid('id'),
                'placeholder' => 'Не заполнено',
            ],
        ]); ?>
    </div>

    <div class="col-md-6">
        <?= $form->field($tableForm, 'default_page_size')->textInput([
            'type' => 'number',
        ]); ?>
    </div>

    <div class="col-md-12">
        <?= $form->field($tableForm, 'visible_in_menu')->checkbox(); ?>
    </div>
</div>
