<?php

use Matodor\RegistryConstructor\models\search\TableRecordSearch;
use Matodor\RegistryConstructor\widgets\RecordsSearch\Widget as RecordsSearchWidget;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/** @var View $this */
/** @var TableRecordSearch $searchModel */
/** @var RecordsSearchWidget $widget */
/** @var string $resetUrl */
/** @var string $actionUrl */

?>

<div class="block-box border-default records-search">
    <div class="block-box__body">
        <?php $form = ActiveForm::begin([
            'id' => uniqid('f'),
            'method' => 'GET',
            'action' => $actionUrl,
            'options' => [
                'class' => 'd-flex flex-column flex-md-row form records-search__form w-100',
            ],
        ]); ?>
            <div class="flex-md-grow-1 pr-md-2 mb-2 mb-md-0">
                <div class="records-search__fields">
                    <?php foreach ($searchModel->fields as $fieldValue) { ?>
                        <?= $this->render('/records/_field', [
                            'form' => $form,
                            'field' => $fieldValue->field,
                            'fieldValue' => $fieldValue,
                            'containerClass' => 'records-search__field',
                            'isInSearchForm' => true,
                        ]) ?>
                    <?php } ?>
                </div>
            </div>

            <div class="d-flex flex-row flex-md-column mx-n1 mx-md-0 my-md-n1">
                <?= Html::submitButton('<i class="fas fa-search"></i>', [
                    'class' => 'btn btn-success flex-grow-1 mx-1 mx-md-0 my-md-1',
                ]) ?>

                <?= Html::a('<i class="fas fa-times"></i>', $resetUrl, [
                    'class' => 'd-flex align-items-center justify-content-center btn btn-outline-secondary flex-grow-0 flex-md-grow-1 mx-1 mx-md-0 my-md-1',
                ]) ?>
            </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
