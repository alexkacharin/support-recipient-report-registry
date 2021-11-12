<?php

use dektrium\user\models\User;
use yii\widgets\ActiveForm;

/** @var ActiveForm $form */
/** @var User $user */

?>

<?= $form->field($user, 'email')->textInput(['maxlength' => 255]) ?>
<?= $form->field($user, 'username')->textInput(['maxlength' => 255]) ?>
<?= $form->field($user, 'password')->passwordInput() ?>
