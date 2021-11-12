<?php

use Matodor\RegistryConstructor\models\data\Float\Settings;
use Matodor\RegistryConstructor\models\data\Float\ValueForm;
use Matodor\RegistryConstructor\models\TableField;
use yii\web\View;
use yii\bootstrap4\ActiveField;
use yii\bootstrap4\ActiveForm;

/** @var View $this */
/** @var TableField $field */
/** @var ValueForm $fieldValue */
/** @var ActiveForm $form */
/** @var ActiveField $formField */

$activeField = $form->field($fieldValue, 'value_float', [
    'inputOptions' => [
        'class' => 'form-control',
        'placeholder' => $field->placeholder,
        'id' => uniqid('f'),
    ],
    'labelOptions' => [
        'class' => 'control-label',
    ],
    'options' => [
        'class' => 'form-group mb-0',
    ],
])->label(false);

if ($field->getHasSettings()
    && $field->settings instanceof Settings
    && $field->settings->getHasPrefixOrPostfix()
) {
    $activeField->template = $field->settings->getActiveFieldTemplate();
}

?>

<?= $activeField->textInput([
    'type' => 'number',
    'step' => '0.01',
]) ?>
