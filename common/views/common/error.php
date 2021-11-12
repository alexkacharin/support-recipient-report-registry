<?php

/** @var yii\web\View $this */
/** @var string $name */
/** @var string $message */
/** @var Exception $exception  */

use yii\helpers\Html;

$this->title = nl2br(Html::encode($message));

?>

<div class="error-page">
    <div class="container">
        <h2 class="mb-4">Ошибка</h2>
        <div class="error-status-code">
            <div class="error-status-code__code"><?= Yii::$app->response->statusCode ?></div>
            <div class="error-status-code__text"><?= nl2br(Html::encode($message)) ?></div>
        </div>

        <div class="text-center my-4">
            Вышеуказанная ошибка произошла, когда веб-сервер обрабатывал ваш запрос. <br>
            Пожалуйста, свяжитесь с нами, если считаете, что это ошибка сервера.
        </div>

        <div class="text-right">
            <?= Html::a('Вернуться на главную', ['/site/index'], [
                'class' => 'btn btn-primary',
            ]); ?>
        </div>
    </div>
</div>
