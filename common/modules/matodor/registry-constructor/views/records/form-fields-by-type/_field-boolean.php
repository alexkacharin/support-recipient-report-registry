<?php

use Matodor\RegistryConstructor\models\data\Boolean\ValueForm;
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

<?= $form->field($fieldValue, 'value_boolean', [
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
])->checkbox([
    'id' => uniqid('f'),
]) ?>
