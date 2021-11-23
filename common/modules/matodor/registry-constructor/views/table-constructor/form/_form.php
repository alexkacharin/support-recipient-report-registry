<?php

use Matodor\RegistryConstructor\assets\RecordCrudAssets;
use Matodor\RegistryConstructor\assets\SortableAssets;
use Matodor\RegistryConstructor\assets\TableConstructorAssets;
use Matodor\RegistryConstructor\models\forms\TableForm;
use Matodor\RegistryConstructor\Module;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/** @var View $this */
/** @var TableForm $tableForm */

RecordCrudAssets::register($this);
TableConstructorAssets::register($this);
SortableAssets::register($this);

\kartik\select2\Select2Asset::register($this);
\kartik\select2\Select2KrajeeAsset::register($this);
\kartik\select2\ThemeBootstrapAsset::register($this);

$this->registerJsVar('tableConstructorSettings', [
    'getFieldUrl' => Module::getInstance()->toRoute([
        'table-constructor/get-field-form',
        'tableId' => $tableForm->isNewRecord ? null : $tableForm->id,
    ], true),
    'getPermissionUrl' => Module::getInstance()->toRoute([
        'table-constructor/get-permission-form',
        'tableId' => $tableForm->isNewRecord ? null : $tableForm->id,
    ], true),
]);

?>

<div class="registry-table__form">
    <?php $form = ActiveForm::begin([
        'id' => uniqid('f'),
        'options' => [
            'class' => 'd-flex flex-column form w-100',
        ],
    ]); ?>
        <?= $form->errorSummary($tableForm, [
            'class' => 'mt-2 mb-1 p-2',
        ]) ?>

        <div class="block-box border-default mt-3 mb-2">
            <div class="block-box__body d-flex flex-column">
                <ul class="nav nav-pills my-0" id="pills-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="pill" href="#pills-common" role="tab" aria-selected="true">
                            Общее
                        </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" data-toggle="pill" href="#pills-other" role="tab" aria-selected="false">
                          Прочее
                      </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="tab-content" id="pills-tabContent">
            <div class="tab-pane fade show active" id="pills-common" role="tabpanel">
                <div class="block-box border-default">
                    <div class="block-box__body d-flex flex-column">
                        <?= $this->render('_table-info', [
                            'form' => $form,
                            'tableForm' => $tableForm,
                        ]) ?>

                        <?= $this->render('_table-permissions', [
                            'form' => $form,
                            'tableForm' => $tableForm,
                        ]) ?>

                        <div class="mt-2">
                            <?= $this->render('_table-fields', [
                                'form' => $form,
                                'tableForm' => $tableForm,
                            ]) ?>
                        </div>

                        <div class="mt-2">
                            <div class="d-flex flex-wrap m-n1">
                                <div class="p-1">
                                    <?= Html::button('Добавить поле', [
                                        'class' => 'btn btn-primary registry-table__form-add-field-btn',
                                    ]) ?>
                                </div>
                                <div class="p-1">
                                    <?= Html::submitButton($tableForm->isNewRecord ? 'Далее' : 'Обновить таблицу', [
                                        'class' => 'btn btn-success',
                                    ]) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="pills-other" role="tabpanel">
                <?= $this->render('_table-other', [
                    'form' => $form,
                    'tableForm' => $tableForm,
                ]) ?>
            </div>
        </div>
    <?php ActiveForm::end(); ?>
</div>
