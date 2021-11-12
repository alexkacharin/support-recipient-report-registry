<?php

use common\models\forms\LoginForm;
use dektrium\user\widgets\Connect;
use yii\bootstrap4\ActiveForm;
use yii\captcha\Captcha;
use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var LoginForm $model
 * @var dektrium\user\Module $module
 */

$this->title = Yii::t('user', 'Sign in');
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="d-flex flex-grow-1 flex-column align-items-center justify-content-center">
    <div class="block-box border-default form-login">
        <div class="block-box__header">
            <h4 class="my-0 font-weight-normal text-center">
                <?= Html::encode($this->title) ?>
            </h4>
        </div>
        <div class="card-body">
            <?php $form = ActiveForm::begin([
                'id' => 'login-form',
                'enableAjaxValidation' => false,
                'enableClientValidation' => false,
                'validateOnBlur' => false,
                'validateOnType' => false,
                'validateOnChange' => false,
            ]) ?>
                <?php if ($module->debug) { ?>
                    <?= $form->field($model, 'login', [
                        'inputOptions' => [
                            'autofocus' => 'autofocus',
                            'class' => 'form-control',
                            'tabindex' => '1',
                        ],
                    ])->dropDownList(LoginForm::loginList()) ?>
                <?php } else { ?>
                    <?= $form->field($model, 'login', [
                        'inputOptions' => [
                            'autofocus' => 'autofocus',
                            'class' => 'form-control',
                            'tabindex' => '1',
                        ],
                    ]) ?>
                <?php } ?>

                <?php if ($module->debug) { ?>
                    <div class="alert alert-warning">
                        <?= Yii::t('user', 'Password is not necessary because the module is in DEBUG mode.'); ?>
                    </div>
                <?php } else { ?>
                    <?= $form->field($model, 'password', [
                        'inputOptions' => [
                            'class' => 'form-control',
                            'tabindex' => '2',
                        ],
                    ])->passwordInput()->label(
                        Yii::t('user', 'Password')
                        . ($module->enablePasswordRecovery ?
                            ' (' . Html::a(
                                Yii::t('user', 'Forgot password?'),
                                ['/user/recovery/request'],
                                ['tabindex' => '5']
                            )
                            . ')' : '')
                    ) ?>
                <?php } ?>

                <?= $form->field($model, 'rememberMe')->checkbox(['tabindex' => '3']) ?>
                <?= $form->field($model, 'captcha')->widget(Captcha::class, [
                    'captchaAction' => ['/site/captcha'],
                    'imageOptions' => [
                        'class' => 'form-login__captcha',
                    ],
                    'options' => [
                        'placeholder' => $model->getAttributeLabel('captcha'),
                    ],
                ]) ?>

                <div class="mt-2">
                    <?= Html::submitButton(
                        Yii::t('user', 'Sign in'),
                        ['class' => 'btn btn-primary btn-block', 'tabindex' => '4']
                    ) ?>
                </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>

    <?php if ($module->enableConfirmation) { ?>
        <p class="text-center">
            <?= Html::a(Yii::t('user', 'Didn\'t receive confirmation message?'), ['/user/registration/resend']) ?>
        </p>
    <?php } ?>

    <?php if ($module->enableRegistration) { ?>
        <p class="text-center">
            <?= Html::a(Yii::t('user', 'Don\'t have an account? Sign up!'), ['/user/registration/register']) ?>
        </p>
    <?php } ?>

    <?= Connect::widget([
        'baseAuthUrl' => ['/user/security/auth'],
    ]) ?>
</div>
