<?php

use dektrium\rbac\models\Assignment;
use kartik\select2\Select2;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Alert;
use yii\helpers\Html;

/** @var Assignment $model */

?>

<?php if ($model->updated) { ?>
    <?= Alert::widget([
        'options' => [
            'class' => 'alert-success'
        ],
        'body' => Yii::t('rbac', 'Assignments have been updated'),
    ]) ?>
<?php } ?>

<?php $form = ActiveForm::begin([
    'enableClientValidation' => false,
    'enableAjaxValidation'   => false,
]) ?>
    <?= Html::activeHiddenInput($model, 'user_id') ?>
    <?= $form->field($model, 'items')->widget(Select2::class, [
        'data' => $model->getAvailableItems(),
        'options' => [
            'id' => 'items',
            'multiple' => true,
        ],
    ]) ?>

    <div class="mt-3">
        <?= Html::submitButton(Yii::t('rbac', 'Update assignments'), [
            'class' => 'btn btn-success',
        ]) ?>
    </div>
<?php ActiveForm::end() ?>

