<?php

use dektrium\rbac\widgets\Menu;
use yii\web\View;

/** @var View $this */
/** @var string $content */

?>

<div class="block-box border-default py-0 mb-4">
    <div class="block-box__body block-box__body_no-padding position-relative">
        <?= Menu::widget() ?>
    </div>
</div>

<div>
    <?= $content ?>
</div>
