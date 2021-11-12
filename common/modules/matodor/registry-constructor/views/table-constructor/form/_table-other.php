<?php

use Matodor\RegistryConstructor\models\forms\TableForm;
use unclead\multipleinput\MultipleInput;
use yii\bootstrap4\ActiveForm;
use yii\web\View;

/** @var View $this */
/** @var TableForm $tableForm */
/** @var ActiveForm $form */

?>

<div class="block-box border-default">
    <div class="block-box__body d-flex flex-column">
        <div class="row">
            <?php if (!$tableForm->isNewRecord) { ?>
                <div class="col-12">
                    <div class="alert alert-info mb-2">
                        Токен таблицы: <b>"<?= $tableForm->token ?>"</b>
                    </div>
                </div>
            <?php } ?>
            <div class="col-12">
                <?= $form->field($tableForm, 'widget_class_viewer')->textInput() ?>
            </div>

            <div class="col-12">
                <?= $form->field($tableForm, 'widget_class_toolbar')->textInput() ?>
            </div>

            <div class="col-12">
                <?= $form->field($tableForm, 'widget_class_search')->textInput() ?>
            </div>
        </div>
    </div>
</div>

<?php foreach ([
    'table_behaviors',
    'records_behaviors',
    'records_assets',
] as $attr) { ?>
    <div class="block-box border-default mt-2">
        <div class="block-box__body d-flex flex-column">
             <?= $form->field($tableForm, $attr, [
                 'options' => [
                    'class' => 'form-group mb-0',
                 ],
             ])->widget(MultipleInput::class, [
                 'allowEmptyList' => true,
                 'addButtonPosition' => MultipleInput::POS_FOOTER,
                 'addButtonOptions' => [
                     'label' => '<i class="fas fa-plus"></i>',
                     'class' => 'btn btn-success',
                 ],
                 'removeButtonOptions' => [
                     'label' => '<i class="fas fa-trash"></i>',
                 ],
             ]) ?>
        </div>
    </div>
<?php } ?>
