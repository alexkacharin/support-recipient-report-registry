<?php

use dektrium\rbac\models\Search;
use kartik\select2\Select2;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Pjax;

/** @var array $dataProvider */
/** @var View $this */
/** @var Search $filterModel */

$this->title = Yii::t('rbac', 'Permissions');
$this->params['breadcrumbs'][] = $this->title;

?>

<?php $this->beginContent('@dektrium/rbac/views/layout.php') ?>
    <?php Pjax::begin() ?>
        <div class="block-box border-default py-0">
            <div class="block-box__body block-box__body_no-padding p-0 position-relative" style="overflow-x: auto;">
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $filterModel,
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
                            'header' => Yii::t('rbac', 'Name'),
                            'options' => [
                                'style' => 'width: 20%'
                            ],
                            'filter' => Select2::widget([
                                'model' => $filterModel,
                                'attribute' => 'name',
                                'data' => $filterModel->getNameList(),
                                'options' => [
                                    'placeholder' => Yii::t('rbac', 'Select permission'),
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true,
                                ],
                            ]),
                        ],
                        [
                            'attribute' => 'description',
                            'header' => Yii::t('rbac', 'Description'),
                            'options' => [
                                'style' => 'width: 55%',
                            ],
                            'filterInputOptions' => [
                                'class' => 'form-control',
                                'id' => null,
                                'placeholder' => Yii::t('rbac', 'Enter the description')
                            ],
                        ],
                        [
                            'attribute' => 'rule_name',
                            'header' => Yii::t('rbac', 'Rule name'),
                            'options' => [
                                'style' => 'width: 20%'
                            ],
                            'filter' => Select2::widget([
                                'model' => $filterModel,
                                'attribute' => 'rule_name',
                                'data' => $filterModel->getRuleList(),
                                'options' => [
                                    'placeholder' => Yii::t('rbac', 'Select rule'),
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true,
                                ],
                            ]),
                        ],
                        [
                            'class' => ActionColumn::class,
                            'template' => '{update}{delete}',
                            'urlCreator' => function ($action, $model) {
                                return Url::to(['/rbac/permission/' . $action, 'name' => $model['name']]);
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
