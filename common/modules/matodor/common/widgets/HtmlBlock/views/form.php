<?php

use dosamigos\tinymce\TinyMce;
use Matodor\Common\models\HtmlBlock;
use yii\helpers\Html;
use yii\web\View;

/** @var View $this */
/** @var HtmlBlock $block */
/** @var boolean $asTextInput */

?>

<div class="html-block">
    <?php if ($asTextInput) { ?>
        <div class="form-group">
            <?= Html::activeTextInput($block, "[{$block->key}]content", [
                'class' => 'form-control',
            ]) ?>
        </div>
    <?php } else { ?>
        <?= TinyMce::widget([
            'model' => $block,
            'attribute' => "[{$block->key}]content",
            'language' => 'ru',
            'id' => uniqid('h'),
            'clientOptions' => [
                'plugins' => 'print preview importcss searchreplace autolink autosave save directionality visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount textpattern noneditable help charmap quickbars',
                'paste_as_text' => true,
                'height' => '200',
            ],
        ]) ?>
    <?php } ?>
</div>
