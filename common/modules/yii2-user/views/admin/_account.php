<?php

use dektrium\user\models\User;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/** @var View $this */
/** @var User $user */

?>

<?php $this->beginContent('@dektrium/user/views/admin/update.php', ['user' => $user]) ?>
    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal',
        'enableAjaxValidation' => true,
        'enableClientValidation' => false,
        'fieldConfig' => [
            'horizontalCssClasses' => [
                'wrapper' => 'col-sm-9',
            ],
        ],
    ]); ?>

        <?= $this->render('_user', [
            'form' => $form,
            'user' => $user,
        ]) ?>

        <div class="mt-3">
            <?= Html::submitButton(Yii::t('user', 'Update'), [
                'class' => 'btn btn-success',
            ]) ?>
        </div>
    <?php ActiveForm::end(); ?>
<?php $this->endContent() ?>
