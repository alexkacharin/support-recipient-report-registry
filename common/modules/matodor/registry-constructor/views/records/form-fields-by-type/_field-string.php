<?php

use Matodor\RegistryConstructor\models\data\String\Settings;
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

if ($field->getHasSettings()
    && $field->settings instanceof Settings
    && $field->settings->getHasPrefixOrPostfix()
) {
    $activeField->template = $field->settings->getActiveFieldTemplate();
}

?>

<?php if ($field->getHasSettings()
    && $field->settings instanceof Settings
    && $field->settings->only_digits
) { ?>
    <?= $activeField->widget(MaskedInput::class, [
        'clientOptions' => [
            'alias' =>  'numeric',
            'rightAlign' => false,
        ],
    ]) ?>
<?php } else { ?>
    <?= $activeField->textInput() ?>
<?php } ?>
