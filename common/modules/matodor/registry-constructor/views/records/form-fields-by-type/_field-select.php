<?php

use kartik\select2\Select2;
use Matodor\Common\components\Helper;
use Matodor\RegistryConstructor\models\data\Select\ValueForm;
use Matodor\RegistryConstructor\models\TableField;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\web\View;
use yii\bootstrap4\ActiveField;
use yii\bootstrap4\ActiveForm;

/** @var View $this */
/** @var TableField $field */
/** @var ValueForm $fieldValue */
/** @var ActiveForm $form */
/** @var ActiveField $formField */
/** @var bool|null $isInSearchForm */

$template = null;

if (!$isInSearchForm && $fieldValue->field->variantsTable->canAddRecords()) {
    $fieldValue->initNewVariantRecord();
    $collapseId = uniqid('collapse-');
    $template = $this->render('_field-select_template', [
        'field' => $field,
        'fieldValue' => $fieldValue,
        'collapseId' => $collapseId,
    ]);
} else {
    $template = "{label}\n{input}\n{hint}\n{error}";
}

?>

<?= $form->field($fieldValue, 'value_record_id', [
    'template' => $template,
    'labelOptions' => [
        'class' => 'control-label',
    ],
    'options' => [
        'class' => 'form-group mb-0',
    ],
])->widget(Select2::class, [
    'data' => $fieldValue->variantsSelectData,
    'pluginLoading' => false,
    'theme' => Select2::THEME_BOOTSTRAP,
    'pluginOptions' => [
        'escapeMarkup' => new JsExpression("function(markup) { return markup; }"),
        'templateSelection' => new JsExpression("function(data) { return data.text; }")
        // 'templateResult' => new JsExpression("function(data) { return data.html; }"),
    ],
    'options' => [
        'id' => uniqid('id'),
        'placeholder' => $field->placeholder,
        'class' => 'form-control variants-selector',
    ],
])->label(false) ?>

<?php if (!$isInSearchForm && $fieldValue->field->variantsTable->canAddRecords()) { ?>
    <?= Html::beginTag('div', [
        'id' => $collapseId,
        'class' => Helper::filterCssClasses([
            'registry-table-record__form registry-table-record__form-variant collapse' => true,
            'show' => $fieldValue->newVariantRecord->hasErrors(),
        ]),
    ]) ?>
        <div class="block-box border-default">
            <div class="block-box__body d-flex flex-column">
                <div class="alert alert-success py-0 px-2 m-0">
                    Добавление нового значения, в справочник <b><?= $fieldValue->newVariantRecord->table->name ?></b>
                </div>
                <?= $this->render('/records/_fields', [
                    'table' => $fieldValue->newVariantRecord->table,
                    'tableRecord' => $fieldValue->newVariantRecord,
                    'form' => $form,
                ]) ?>
            </div>
        </div>
    <?= Html::endTag('div') ?>
<?php } ?>
