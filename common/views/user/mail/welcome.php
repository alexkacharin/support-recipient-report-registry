<?php

use yii\helpers\Html;

/**
 * @var dektrium\user\Module $module
 * @var dektrium\user\models\User $user
 * @var dektrium\user\models\Token $token
 * @var bool $showPassword
 */
?>
<p style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.6; font-weight: normal; margin: 0 0 10px; padding: 0;">
    Здравствуйте,
    <br />
    Ваш аккаунт на сайте <?= Yii::$app->name ?> был успешно создан.
    Мы сгенерировали аккаунт для вас:
    Логин: <strong><?= $user->username ?></strong>
    <?php if ($showPassword || $module->enableGeneratingPassword) { ?>
        Пароль: <strong><?= $user->password ?></strong>
    <?php } ?>

    <?php if ($token !== null) { ?>
        Чтобы активировать ваш аккаунт, пожалуйста, нажмите на ссылку ниже.
        <br />
        <strong><?= Html::a(Html::encode($token->url), $token->url) ?></strong>
        Если вы не можете нажать на ссылку, скопируйте ее и вставьте в адресную строку вашего браузера.
        <br />
    <?php } ?>
</p>

<p style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.6; font-weight: normal; margin: 0 0 10px; padding: 0;">
    <strong>P.S. Если вы получили это сообщение по ошибке, просто удалите его.</strong>
</p>
