<?php

use dektrium\rbac\widgets\Assignments;
use dektrium\user\models\User;
use yii\web\View;

/** @var View $this */
/** @var User $user */

?>

<?php $this->beginContent('@dektrium/user/views/admin/update.php', ['user' => $user]) ?>
    <?= yii\bootstrap4\Alert::widget([
        'options' => [
            'class' => 'alert-info alert-dismissible',
        ],
        'body' => Yii::t('user', 'You can assign multiple roles or permissions to user by using the form below'),
    ]) ?>

    <?= Assignments::widget([
        'userId' => $user->id,
    ]) ?>
<?php $this->endContent() ?>
