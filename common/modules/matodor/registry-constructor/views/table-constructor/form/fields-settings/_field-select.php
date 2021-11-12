<?php

use Matodor\RegistryConstructor\models\data\Select\Settings;
use Matodor\RegistryConstructor\models\forms\TableFieldForm;
use yii\bootstrap4\ActiveForm;
use yii\web\View;

/** @var View $this */
/** @var TableFieldForm $tableFieldForm */
/** @var ActiveForm $form */
/** @var Settings $settings */

?>

<div class="row mx-n1">
    <?php if ($tableFieldForm->getHasVariantsTable() && !empty($tableFieldForm->variantsTable->fields)) { ?>
        <div class="col-md-12 px-1">
            <?php
                $templateId = "template-{$tableFieldForm->uid}";
                $templateParams = $this->render('_field-select_params', [
                    'tableFieldForm' => $tableFieldForm,
                    'settings' => $settings,
                    'templateId' => $templateId,
                ]);
            ?>

            <?= $form->field($settings, 'template', [
                'template' => "{label}\n{$templateParams}\n{input}\n{hint}\n{error}",
            ])->textarea([
                'id' => $templateId,
                'placeholder' => 'Пример: ${Наименование}',
                'class' => 'form-control registry-table__form-field-template-input',
            ]) ?>

            <?= $form->field($settings, 'template_in_table')->textarea([
                'id' => uniqid('id'),
                'placeholder' => 'Пример: ${<FormattedValue>}, ${URL}',
            ]) ?>
        </div>
    <?php } ?>
</div>
