<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use common\models\forms\RegistrationForm;
use yii\captcha\Captcha;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var RegistrationForm $model
 * @var dektrium\user\Module $module
 */

$this->title = Yii::t('user', 'Sign up');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="row">
    <?php $form = ActiveForm::begin([
        'id' => 'registration-form',
        'enableAjaxValidation' => false,
        'enableClientValidation' => false,
        'options' => [
            'style' => 'width: 100%',
            'class' => 'd-flex'
        ]
    ]); ?>
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?= Html::encode($this->title) ?></h3>
            </div>
            <div class="panel-body">
                <?= $form->field($model, 'email') ?>
                <?= $form->field($model, 'inn')->textInput(['id' => 'register-inn']) ?>

                <?php if ($module->enableGeneratingPassword == false): ?>
                    <?= $form->field($model, 'password')->passwordInput() ?>
                <?php endif ?>

                <?= $form->field($model, 'captcha')->widget(Captcha::class, [
                    'captchaAction' => ['/site/captcha']
                ]) ?>

                <?= Html::button('+', ['id' => 'btn_add', 'class' => 'btn btn-primary']); ?>
                <?= Html::submitButton('Регистрация', ['class' => 'btn btn-success']) ?>
            </div>
        </div>
    </div>
    <div class="col-md-6">

        <?= $form->field($model, 'companyName')->textInput() ?>
        <?= $form->field($model, 'location')->textInput() ?>
        <p></p>
    </div>

    <div class="col-lg-12">

    </div>
    <?php ActiveForm::end(); ?>

</div>
<p class="text-center">
    <?= Html::a(Yii::t('user', 'Already registered? Sign in!'), ['/user/security/login']) ?>
</p>
