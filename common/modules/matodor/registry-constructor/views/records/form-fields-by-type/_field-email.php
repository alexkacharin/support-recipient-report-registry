<?php

use Matodor\RegistryConstructor\models\data\String\ValueForm;
use Matodor\RegistryConstructor\models\TableField;
use yii\web\View;
use yii\bootstrap4\ActiveField;
use yii\bootstrap4\ActiveForm;
use yii\widgets\MaskedInput;

/** @var View $this */
/** @var TableField $field */
/** @var ValueForm $fieldValue */
/** @var ActiveForm $form */
/** @var ActiveField $formField */
/** @var bool|null $isInSearchForm */

$activeField = $form->field($fieldValue, 'value_string', [
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

?>

<?php if ($isInSearchForm) { ?>
    <?= $activeField->textInput() ?>
<?php } else { ?>
    <?= $activeField->widget(MaskedInput::class, [
        'clientOptions' => [
            'alias' => 'email',
        ],
    ]) ?>
<?php } ?>
