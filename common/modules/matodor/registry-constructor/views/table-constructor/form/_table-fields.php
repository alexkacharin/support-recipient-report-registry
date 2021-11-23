<?php

use Matodor\RegistryConstructor\models\forms\TableForm;
use yii\bootstrap4\ActiveForm;
use yii\web\View;

/** @var View $this */
/** @var TableForm $tableForm */
/** @var ActiveForm $form */

?>

<div><h5>Поля таблицы:</h5></div>

<div class="registry-table__form-fields form-list">
    <?php foreach ($tableForm->editFields as $editField) { ?>
        <div class="registry-table__form-field form-list__item" data-uid="<?= $editField->uid ?>">
            <?= $this->render('_table-field', [
                'tableFieldForm' => $editField,
                'tableForm' => $tableForm,
                'form' => $form,
            ]) ?>
        </div>
    <?php } ?>
</div>
