<?php

use Matodor\RegistryConstructor\models\data\String\Settings;
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
        <?= $form->field($settings, 'min_length')->input('number', ['placeholder' => 'Длина не задана']); ?>
        <?= $form->field($settings, 'max_length')->input('number', ['placeholder' => 'Длина не задана']); ?>
        <?= $form->field($settings, 'input_prefix')->textInput(['placeholder' => 'Пусто']); ?>
        <?= $form->field($settings, 'input_postfix')->textInput(['placeholder' => 'Пусто']); ?>

        <?= $form->field($settings, 'template')->textarea([
            'id' => uniqid('id'),
            'placeholder' => 'Пример: ${<Value>}',
        ]); ?>

        <?= $form->field($settings, 'template_in_table')->textarea([
            'id' => uniqid('id'),
            'placeholder' => 'Пример: ${<FormattedValue>}',
        ]); ?>

        <?= $form->field($settings, 'only_digits')->checkbox(); ?>
        <?= $form->field($settings, 'allow_html')->checkbox(); ?>
    </div>
</div>
