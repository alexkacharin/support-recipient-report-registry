<?php

use Matodor\RegistryConstructor\models\TableField;
use Matodor\RegistryConstructor\models\TableRecordValue;
use Matodor\RegistryConstructor\traits\TableRecordValueFormTrait;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\web\View;

/** @var bool|null $isInSearchForm */
/** @var string $containerClass */
/** @var View $this */
/** @var TableField $field */
/** @var TableRecordValue|TableRecordValueFormTrait $fieldValue */
/** @var ActiveForm $form */

$isInSearchForm = $isInSearchForm ?? false;
$containerClass = $containerClass ?? 'form-list__item py-2';

?>

<?= Html::beginTag('div', [
    'class' => "registry-table-record__form-field $containerClass",
    'data-field-id' => $field->id,
    'data-field-type' => Inflector::camel2id($field->getValueTypeDefinition()->type),
]) ?>
    <?php if (!$isInSearchForm) { ?>
        <?= Html::activeHiddenInput($fieldValue, 'id', [
            'id' => uniqid('id-'),
        ]) ?>
        <?= Html::activeHiddenInput($fieldValue, 'registry_table_field_id', [
            'id' => uniqid('id-'),
        ]) ?>
    <?php } ?>

    <label class="control-label registry-table-record__form-field-name">
        <?= $field->name ?>
    </label>

    <?= $this->render($fieldValue->getFormView(), [
        'field' => $field,
        'fieldValue' => $fieldValue,
        'form' => $form,
        'isInSearchForm' => $isInSearchForm,
    ]) ?>
<?= Html::endTag('div') ?>

