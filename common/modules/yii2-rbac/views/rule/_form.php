<?php

use dektrium\rbac\models\Rule;
use yii\bootstrap4\ActiveForm;
use yii\web\View;
use yii\helpers\Html;

/** @var View $this */
/** @var Rule $model */

?>

<div class="block-box border-default">
    <div class="block-box__body">
        <?php $form = ActiveForm::begin([
            'enableClientValidation' => false,
            'enableAjaxValidation'   => true,
        ]) ?>
            <?= $form->field($model, 'name') ?>
            <?= $form->field($model, 'class') ?>
            <div class="mt-3">
                <?= Html::submitButton(Yii::t('rbac', 'Save'), ['class' => 'btn btn-success btn-block']) ?>
            </div>
        <?php ActiveForm::end() ?>
    </div>
</div>
