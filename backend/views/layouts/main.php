<?php

use Matodor\Common\components\Helper;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Breadcrumbs;
use backend\assets\AppAsset;
use common\widgets\Alert;

/** @var View $this */
/** @var string $content */

$title = Html::encode($this->title . ' | ' . Yii::$app->name);
AppAsset::register($this);

?>

<?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>">
        <head>
            <meta charset="<?= Yii::$app->charset ?>">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title><?= $title ?></title>
            <?php $this->registerCsrfMetaTags() ?>
            <?php $this->head() ?>
        </head>

        <?= Html::beginTag('body', [
            'class' => Helper::filterCssClasses([
                'd-flex flex-column' => true,
                str_replace('/', '_', Yii::$app->controller->route) => true,
                $this->params['bodyCssClass'] ?? '' => $this->params['bodyCssClass'] ?? false,
            ]),
        ]) ?>
            <?php $this->beginBody() ?>
                <?= $this->render('_header') ?>

                <div class="<?= $this->params['containerClass'] ?> d-flex flex-column flex-grow-1 py-3">
                    <?= Alert::widget([
                        'options' => ['class' => 'mb-2']
                    ]) ?>

                    <?php if (!Yii::$app->user->isGuest) { ?>
                        <?= Breadcrumbs::widget([
                            'itemTemplate' => '<li class="breadcrumb-item">{link}</li>',
                            'activeItemTemplate' => '<li class="breadcrumb-item active">{link}</li>',
                            'links' => $this->params['breadcrumbs'] ?? [],
                            'options' => ['class' => 'breadcrumb mb-2']
                        ]) ?>
                    <?php } ?>

                    <?= $content ?>
                </div>

                <?= $this->render('_footer') ?>
            <?php $this->endBody() ?>

        <?= Html::endTag('body') ?>
    </html>
<?php $this->endPage() ?>
