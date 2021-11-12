<?php

use Matodor\RegistryConstructor\models\forms\TableRecordForm;
use Matodor\RegistryConstructor\models\Table;
use yii\bootstrap4\ActiveForm;
use yii\web\View;

/** @var View $this */
/** @var TableRecordForm $tableRecord */
/** @var Table $table */
/** @var ActiveForm $form */

?>

<div class="registry-table-record__form-fields form-list">
    <?php foreach ($table->fields as $field) { ?>
        <?php if (isset($tableRecord->editValues[$field->id])) { ?>
            <?php
                $tableRecord
                    ->editValues[$field->id]
                    ->populateRelationIfNeeded('field', $field);
            ?>

            <?= $this->render('_field', [
                'field' => $field,
                'fieldValue' => $tableRecord->editValues[$field->id],
                'form' => $form,
            ]) ?>
        <?php } ?>
    <?php } ?>
</div>

