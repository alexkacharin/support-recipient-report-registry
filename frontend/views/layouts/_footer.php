<?php

use Matodor\Common\components\Helper;
use yii\helpers\Html;
use yii\web\View;

/** @var View $this */

?>

<footer class="footer">
    <div class="container">
        <div>&copy; <?= date('Y') ?></div>
        <div class="mt-2">
            <?= Html::a('Вход для администрации', Helper::to(['/site/index'], 'backend', true), [
                'class' => 'btn btn-primary',
            ]) ?>
        </div>
    </div>
</footer>
