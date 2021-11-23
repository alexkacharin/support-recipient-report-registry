<?php

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Nav;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var dektrium\user\models\User $user */

$this->title = Yii::t('user', 'Create a user account');
$this->params['breadcrumbs'][] = ['label' => Yii::t('user', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<?= $this->render('/_alert', ['module' => Yii::$app->getModule('user'),]) ?>
<?= $this->render('/admin/_menu') ?>

<div class="row">
    <div class="col-md-3">
        <div class="block-box border-default">
            <div class="block-box__body position-relative">
                <?= Nav::widget([
                    'options' => [
                        'class' => 'nav-pills flex-column',
                    ],
                    'items' => [
                        [
                            'label' => Yii::t('user', 'Account details'),
                            'url' => ['/user/admin/create'],
                        ],
                        [
                            'label' => Yii::t('user', 'Profile details'),
                            'options' => [
                             'class' => 'disabled',
                                'onclick' => 'return false;',
                            ],
                        ],
                        [
                            'label' => Yii::t('user', 'Information'),
                            'options' => [
                                'class' => 'disabled',
                                'onclick' => 'return false;',
                            ],
                        ],
                    ],
                ]) ?>
            </div>
        </div>
    </div>

    <div class="col-md-9">
        <div class="block-box border-default">
            <div class="block-box__body position-relative">
                <div class="alert alert-info">
                    <?= Yii::t('user', 'Credentials will be sent to the user by email') ?>.
                    <?= Yii::t('user', 'A password will be generated automatically if not provided') ?>.
                </div>
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
                        <?= Html::submitButton(Yii::t('user', 'Save'), [
                            'class' => 'btn btn-success',
                        ]) ?>
                    </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
