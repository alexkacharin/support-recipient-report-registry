<?php

use dektrium\rbac\models\Role;
use kartik\select2\Select2;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;
use yii\helpers\Html;

/** @var View $this */
/** @var Role $model */

?>

<div class="block-box border-default">
    <div class="block-box__body">
        <?php $form = ActiveForm::begin([
            'enableClientValidation' => false,
            'enableAjaxValidation'   => true,
        ]) ?>

        <?= $form->field($model, 'name') ?>
        <?= $form->field($model, 'description')->textarea() ?>
        <?= $form->field($model, 'rule')->widget(Select2::className(), [
            'options'   => [
                'placeholder' => Yii::t('rbac', 'Select rule'),
            ],
            'pluginOptions' => [
                'ajax' => [
                    'url'  => Url::to(['/rbac/rule/search']),
                    'data' => new JsExpression('function(params) { return {q:params.term}; }')
                ],
                'allowClear' => true,
            ],
        ]) ?>

        <?php if ($model->dataCannotBeDecoded) { ?>
            <div class="alert alert-info">
                <?= Yii::t('rbac', 'Data cannot be decoded') ?>
            </div>
        <?php } else { ?>
            <?= $form->field($model, 'data')->textarea([
                'rows' => 3
            ]) ?>
        <?php } ?>

        <?= $form->field($model, 'children')->widget(Select2::className(), [
            'data' => $model->getUnassignedItems(),
            'options' => [
                'id' => 'children',
                'multiple' => true
            ],
        ]) ?>

        <div class="mt-3">
            <?= Html::submitButton(Yii::t('rbac', 'Save'), [
                'class' => 'btn btn-success',
            ]) ?>
        </div>

        <?php ActiveForm::end() ?>
    </div>
</div>
