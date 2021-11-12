<?php

use kartik\date\DatePicker;
use Matodor\RegistryConstructor\models\data\Date\ValueForm;
use Matodor\RegistryConstructor\models\TableField;
use yii\web\View;
use yii\bootstrap4\ActiveField;
use yii\bootstrap4\ActiveForm;

/** @var View $this */
/** @var TableField $field */
/** @var ValueForm $fieldValue */
/** @var ActiveForm $form */
/** @var ActiveField $formField */

?>

<?= $form->field($fieldValue, 'value_date', [
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
])->widget(DatePicker::class, [
    'convertFormat' => false,
    'pluginOptions' => [
        'todayHighlight' => true,
        'todayBtn' => true,
        'format' => 'dd.mm.yyyy',
    ],
    'options' => [
        'id' => uniqid('id'),
        'placeholder' => $field->placeholder,
    ],
])->label(false) ?>
