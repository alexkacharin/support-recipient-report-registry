<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

/**
 * @var $this         \yii\web\View
 * @var $searchModel  \dektrium\rbac\models\RuleSearch
 * @var $dataProvider \yii\data\ArrayDataProvider
 */

use kartik\select2\Select2;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\Pjax;

$this->title = Yii::t('rbac', 'Rules');
$this->params['breadcrumbs'][] = $this->title;

?>

<?php $this->beginContent('@dektrium/rbac/views/layout.php') ?>
    <?php Pjax::begin() ?>
        <div class="block-box border-default py-0">
            <div class="block-box__body block-box__body_no-padding p-0 position-relative" style="overflow-x: auto;">
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'pager' => [
                        'options' => ['class' => 'pagination justify-content-center'],
                        'disabledListItemSubTagOptions' => ['class' => 'page-link'],
                        'linkContainerOptions' => ['class' => 'page-item'],
                        'linkOptions' => ['class' => 'page-link'],
                    ],
                    'tableOptions' => [
                        'class' => 'table table-striped mb-0',
                    ],
                    'layout' => '{items}{pager}',
                    'columns' => [
                        [
                            'attribute' => 'name',
                            'label' => Yii::t('rbac', 'Name'),
                            'options' => [
                                'style' => 'width: 20%'
                            ],
                            'filter' => Select2::widget([
                                'model' => $searchModel,
                                'attribute' => 'name',
                                'options' => [
                                    'placeholder' => Yii::t('rbac', 'Select rule'),
                                ],
                                'pluginOptions' => [
                                    'ajax' => [
                                        'url' => Url::to(['search']),
                                        'dataType' => 'json',
                                        'data' => new JsExpression('function(params) { return {q:params.term}; }')
                                    ],
                                    'allowClear' => true,

                                ],
                            ]),
                        ],
                        [
                            'attribute' => 'class',
                            'label' => Yii::t('rbac', 'Class'),
                            'value' => function ($row) {
                                $rule = unserialize($row['data']);

                                return get_class($rule);
                            },
                            'options' => [
                                'style' => 'width: 20%'
                            ],
                        ],
                        [
                            'attribute' => 'created_at',
                            'label' => Yii::t('rbac', 'Created at'),
                            'format' => 'datetime',
                            'options' => [
                                'style' => 'width: 20%'
                            ],
                        ],
                        [
                            'attribute' => 'updated_at',
                            'label' => Yii::t('rbac', 'Updated at'),
                            'format' => 'datetime',
                            'options' => [
                                'style' => 'width: 20%'
                            ],
                        ],
                        [
                            'class' => ActionColumn::class,
                            'template' => '{update}{delete}',
                            'urlCreator' => function ($action, $model) {
                                return Url::to(['/rbac/rule/' . $action, 'name' => $model['name']]);
                            },
                            'options' => [
                                'style' => 'width: 5%'
                            ],
                            'buttons' => [
                                'update' => function ($url) {
                                    return Html::a('<i class="fa fa-edit"></i>', $url, [
                                        'class' => 'btn btn-sm btn-primary m-1',
                                        'target' => '_blank',
                                    ]);
                                },
                                'delete' => function ($url) {
                                    return Html::a('<i class="fa fa-trash"></i>', $url, [
                                        'class' => 'btn btn-sm btn-danger m-1',
                                        'data' => [
                                            'confirm' => Yii::t('user', 'Are you sure?'),
                                            'method' => 'post',
                                        ],
                                    ]);
                                },
                            ],
                        ],
                    ],
                ]) ?>
            </div>
        </div>
    <?php Pjax::end() ?>
<?php $this->endContent() ?>
