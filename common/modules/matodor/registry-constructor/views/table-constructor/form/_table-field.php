<?php

/** @noinspection PhpClassConstantAccessedViaChildClassInspection */

use kartik\select2\Select2;
use Matodor\Common\components\Helper;
use Matodor\RegistryConstructor\components\ValueType;
use Matodor\RegistryConstructor\models\forms\TableFieldForm;
use Matodor\RegistryConstructor\models\forms\TableForm;
use Matodor\RegistryConstructor\Module;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/** @var View $this */
/** @var TableFieldForm $tableFieldForm */
/** @var TableForm $tableForm */
/** @var ActiveForm $form */
/** @var array $_params_ */

?>

<div class="py-2">
    <div class="form-list__item-buttons registry-table__form-field-buttons d-flex justify-content-end m-n1">
        <div class="m-1 px-1 d-flex align-items-center registry-table__form-field-name">
            <?= $tableFieldForm->name ?>
        </div>

        <a class="m-1 px-3 btn btn-secondary btn-sm empty-child"
            href="#collapseField-<?= $tableFieldForm->uid ?>"
            role="button"
            data-toggle="collapse"
            aria-expanded="false"
        ></a>

        <div class="m-1 px-3 btn btn-secondary btn-sm form-list__item-move-item-btn">
            <i class="fas fa-sort"></i>
        </div>

        <div class="m-1 px-3 btn btn-danger btn-sm form-list__item-remove-item-btn">
            <i class="fas fa-trash"></i>
        </div>
    </div>
</div>

<?= Html::activeHiddenInput($tableFieldForm, 'id') ?>
<?= Html::activeHiddenInput($tableFieldForm, 'registry_table_id') ?>

<?php if ($tableFieldForm->hasErrors()) { ?>
    <?= Html::errorSummary($tableFieldForm) ?>
<?php } ?>

<?= Html::beginTag('div', [
    'id' => "collapseField-{$tableFieldForm->uid}",
    'class' => Helper::filterCssClasses([
        'collapse' => true,
        'show' => $tableFieldForm->isNewRecord || $tableFieldForm->hasErrors(),
    ]),
]) ?>
    <div class="row pb-2 mx-n1">
        <div class="col-md-6 px-1">
            <?= $form->field($tableFieldForm, 'name')->textInput([
                'placeholder' => 'Не заполнено',
                'data-field-input' => 'name',
            ]); ?>
        </div>
        <div class="col-md-6 px-1">
            <?= $form->field($tableFieldForm, 'placeholder')->textInput([
                'placeholder' => 'Не заполнено',
                'data-field-input' => 'placeholder',
            ]); ?>
        </div>
        <div class="col-md-6 px-1">
            <?= $form->field($tableFieldForm, 'type')->widget(Select2::class, [
                'data' => $tableFieldForm->getTypeHeaders(),
                'pluginLoading' => false,
                'theme' => Select2::THEME_BOOTSTRAP,
                'disabled' => !$tableFieldForm->isNewRecord,
                'options' => [
                    'id' => uniqid('id'),
                    'placeholder' => 'Не заполнено',
                    'class' => 'registry-table__form-field-force-render',
                    'data-field-input' => 'type',
                ],
            ]); ?>
        </div>
        <div class="col-md-6 px-1">
            <?php if ($tableFieldForm->type === TableFieldForm::FIELD_TYPE_SELECT
                || $tableFieldForm->type === TableFieldForm::FIELD_TYPE_CHILD_RECORD
            ) { ?>
                <?= $form->field($tableFieldForm, 'registry_variants_table_id')->widget(Select2::class, [
                    'data' => $tableFieldForm->variantsTableSelectData,
                    'pluginLoading' => false,
                    'theme' => Select2::THEME_BOOTSTRAP,
                    'disabled' => !$tableFieldForm->isNewRecord,
                    'options' => [
                        'id' => uniqid('id'),
                        'placeholder' => 'Не заполнено',
                        'class' => 'registry-table__form-field-force-render',
                        'data-field-input' => 'registry_variants_table_id',
                    ],
                ]); ?>
            <?php } else { ?>
                <?= $form->field($tableFieldForm, 'value_type')->widget(Select2::class, [
                    'data' => ValueType::instance()->getValueTypesSelectData(),
                    'pluginLoading' => false,
                    'theme' => Select2::THEME_BOOTSTRAP,
                    'disabled' => !$tableFieldForm->isNewRecord,
                    'options' => [
                        'id' => uniqid('id'),
                        'placeholder' => 'Не заполнено',
                        'class' => 'registry-table__form-field-force-render',
                        'data-field-input' => 'value_type',
                    ],
                ]); ?>
            <?php } ?>
        </div>

        <?php if ($tableFieldForm->isVisibleVariantsTableButtons) { ?>
            <div class="col-md-6 px-1">
                <div class="mt-2">
                    <?= Html::a('Редактировать справочник - ' . $tableFieldForm->variantsTable->name,
                        Module::getInstance()->toRoute([
                            'table-constructor/edit',
                            'id' => $tableFieldForm->variantsTable->id,
                        ]),
                        [
                            'class' => 'btn btn-info d-block',
                            'target' => '_blank',
                        ]
                    ) ?>
                </div>
            </div>

            <div class="col-md-6 px-1">
                <div class="my-2">
                    <?= Html::a('Управление записями', Module::getInstance()->toRoute([
                        'records/index',
                        'tableId' => $tableFieldForm->variantsTable->id,
                    ]), [
                        'class' => 'btn btn-info d-block',
                        'target' => '_blank',
                    ]) ?>
                </div>
            </div>
        <?php } ?>

        <?php if ($tableFieldForm->type !== null
            && $tableFieldForm->value_type !== null
        ) { ?>
            <div class="col-md-6 px-1">
                <?php if ($tableFieldForm->type !== TableFieldForm::FIELD_TYPE_CHILD_RECORD) { ?>
                    <?= $form->field($tableFieldForm, 'required')->checkbox(); ?>
                <?php } ?>

                <?php foreach ($tableFieldForm->getAvailableFlags() as $flag) { ?>
                    <?= $form->field($tableFieldForm, $flag)->checkbox(); ?>
                <?php } ?>
            </div>
        <?php } ?>

        <?php if ($tableFieldForm->getHasSettings()) { ?>
            <?php if (($settingsView = $tableFieldForm->settings->getFieldSettingsView($this)) !== null) { ?>
                <div class="col-md-6 px-1">
                    <?= $this->render($settingsView, [
                        'tableFieldForm' => $tableFieldForm,
                        'form' => $form,
                        'settings' => $tableFieldForm->settings,
                    ]) ?>
                </div>
            <?php } ?>
        <?php } ?>

        <div class="col-md-6 px-1 mt-2">
            <?= Html::a('Значение поля по умолчанию', '#collapseDefault-' . $tableFieldForm->uid, [
                'role' => 'button',
                'data-toggle' => 'collapse',
                'aria-expanded' => 'false',
                'class' => Helper::filterCssClasses([
                    'btn w-100' => true,
                    'btn-secondary' => $tableForm->editDefaultValuesRecord === null
                        || !$tableForm->editDefaultValuesRecord->hasErrors('editValues'),
                    'btn-danger' => $tableForm->editDefaultValuesRecord !== null
                        && $tableForm->editDefaultValuesRecord->hasErrors('editValues'),
                ]),
            ]) ?>

            <?= Html::beginTag('div', [
                'id' => "collapseDefault-{$tableFieldForm->uid}",
                'class' => Helper::filterCssClasses([
                    'collapse' => true,
                    'show' => $tableForm->editDefaultValuesRecord !== null
                        && $tableForm->editDefaultValuesRecord->hasErrors('editValues'),
                ]),
            ]) ?>
                <div class="mt-2">
                    <?php if (!$tableFieldForm->isNewRecord
                        && $tableForm->editDefaultValuesRecord !== null
                        && isset($tableForm->editDefaultValuesRecord->editValues[$tableFieldForm->id])
                    ) { ?>
                        <div class="registry-table-record__form-fields form-list">
                            <?= $this->render('/records/_field', [
                                'field' => $tableFieldForm,
                                'fieldValue' => $tableForm->editDefaultValuesRecord->editValues[$tableFieldForm->id],
                                'form' => $form,
                            ]) ?>
                        </div>
                    <?php } else { ?>
                        <div class="alert alert-warning mb-0">
                            Значение по умолчанию можно задать после создания таблицы или после сохранения нового поля!
                        </div>
                    <?php } ?>
                </div>
            <?= Html::endTag('div') ?>
        </div>
    </div>
<?= Html::endTag('div') ?>
