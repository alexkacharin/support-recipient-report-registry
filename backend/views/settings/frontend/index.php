<?php

use Matodor\Common\widgets\HtmlBlock\FormWidget as HtmlBlockForm;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/** @var View $this */

$this->title = 'Настройки главной страницы';
$this->params['breadcrumbs'][] = ['label' => 'Настройки'];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="block-box border-default mb-4">
    <div class="block-box__body">
        <?php $form = ActiveForm::begin([
            'id' => uniqid('f'),
            'options' => ['class' => 'd-flex flex-column form w-100'],
        ]); ?>
            <div>
                <h5>Заголовок</h5>
                <?= HtmlBlockForm::widget([
                    'key' => 'frontend_header_1',
                    'defaultContent' => 'Добро пожаловать!',
                    'asTextInput' => true,
                ]) ?>

                <h5 class="mt-4">Подзаголовок</h5>
                <?= HtmlBlockForm::widget([
                    'key' => 'frontend_subheader_1',
                    'defaultContent' => 'Подзаголовок',
                    'asTextInput' => true,
                ]) ?>

                <h5 class="mt-4">Описание</h5>
                <?= HtmlBlockForm::widget([
                    'key' => 'frontend_description',
                    'defaultContent' => 'Описание',
                    'asTextInput' => false,
                ]) ?>
            </div>

            <div class="mt-2">
                <?= Html::submitButton('Обновить', [
                    'class' => 'btn btn-success',
                ]) ?>
            </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
