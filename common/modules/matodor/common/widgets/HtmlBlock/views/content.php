<?php

use Matodor\Common\models\HtmlBlock;
use yii\helpers\Html;
use yii\web\View;

/** @var View $this */
/** @var HtmlBlock $block */
/** @var boolean $encode */
/** @var array $containerOptions */
/** @var string|null $tag */

?>

<?= Html::beginTag($tag, $containerOptions) ?>
    <?= $encode ? Html::encode($block->content) : $block->content ?>
<?= Html::endTag($tag) ?>
