<?php

use Matodor\Common\components\Helper;
use Matodor\RegistryConstructor\models\search\TableSearch;
use Matodor\RegistryConstructor\models\Table;
use Matodor\RegistryConstructor\Module;
use Matodor\RegistryConstructor\Module as RegistryModule;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

/** @var TableSearch $searchModel */
/** @var View $this */
/** @var ActiveDataProvider $dataProvider */

?>

<div class="table-constructor-index">
    <div class="top-toolbar">
        <div class="btn-group" role="group">
            <?= Html::a('Добавить таблицу', ['create'], [
                'class' => 'btn btn-success',
            ]) ?>
        </div>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pager' => Yii::$app->params['pagerParams'],
        'sorter' => [
            'attributes' => [
                'created_at',
                'updated_at',
                'name',
            ],
            'options' => [
                'class' => 'sorter',
                'itemOptions' => ['class' => '']
            ],
        ],
        'layout' => Yii::$app->params['gridViewLayout'],
        'tableOptions' => [
            'class' => 'grid-view-table table table-striped',
        ],
        'columns' => [
            [
                'enableSorting' => false,
                'header' => $searchModel->getAttributeLabel('name'),
                'content' => function ($model) {
                    /** @var Table $model */
                    return $this->render('table/_column-name', ['table' => $model]);
                },
            ],

            [
                'enableSorting' => false,
                'header' => 'Описание',
                'content' => function ($model) {
                    /** @var Table $model */
                    return $this->render('table/_column-info', ['table' => $model]);
                },
            ],

            Helper::gridViewButtons(null, [
                'template' => '<div>{open}{update}{delete}</div><div>{migration}{migration-data}</div>',
                'buttons' => [
                    'open' => function ($url, $model) {
                        /** @var Table $model */
                        $url = Module::getInstance()->toRoute([
                            'records/index',
                            'tableId' => $model->primaryKey,
                        ]);

                        return Html::a('<i class="fa fa-eye"></i>', $url, [
                            'class' => 'btn btn-sm btn-info m-1',
                            'title' => 'Просмотр записей',
                            'data-toggle' => 'tooltip',
                            'data-placement' => 'top',
                        ]);
                    },
                    'update' => function ($url, $model) {
                        /** @var Table $model */
                        $url = Module::getInstance()->toRoute([
                            'table-constructor/edit',
                            'id' => $model->primaryKey,
                        ]);

                        return Html::a('<i class="fa fa-edit"></i>', $url, [
                            'class' => 'btn btn-sm btn-primary m-1',
                            'target' => '_blank',
                            'title' => 'Редактировать',
                            'data-toggle' => 'tooltip',
                            'data-placement' => 'top',
                        ]);
                    },
                    'delete' => function ($url, $model) {
                        /** @var ActiveRecord $model */
                        $url = Module::getInstance()->toRoute([
                            'table-constructor/confirm-delete',
                            'id' => $model->primaryKey,
                        ]);

                        return Html::a('<i class="fa fa-trash"></i>', $url, [
                            'class' => 'btn btn-sm btn-danger m-1',
                            'title' => 'Удалить',
                            'data-toggle' => 'tooltip',
                            'data-placement' => 'top',
                        ]);
                    },
                    'migration' => function ($url, $model) {
                        /** @var ActiveRecord $model */
                        $url = Module::getInstance()->toRoute([
                            'table-migrations/generate-table-structure',
                            'id' => $model->primaryKey,
                        ]);

                        return Html::a('<i class="fa fa-hammer"></i>', $url, [
                            'class' => 'btn btn-sm btn-secondary m-1',
                            'title' => 'Сгенерировать структуру',
                            'data-toggle' => 'tooltip',
                            'data-placement' => 'top',
                        ]);
                    },
                    'migration-data' => function ($url, $model) {
                        /** @var ActiveRecord $model */
                        $url = Module::getInstance()->toRoute([
                            'table-migrations/generate-table-data',
                            'id' => $model->primaryKey,
                        ]);

                        return Html::a('<i class="fa fa-hammer"></i>', $url, [
                            'class' => 'btn btn-sm btn-secondary m-1',
                            'title' => 'Сгенерировать данные',
                            'data-toggle' => 'tooltip',
                            'data-placement' => 'top',
                        ]);
                    },
                ],
                'visibleButtons' => [
                    'migration' => Yii::$app->user->can('superadmin'),
                    'migration-data' => Yii::$app->user->can('superadmin'),
                    'update' => Yii::$app->user->can(RegistryModule::CAN_EDIT_CONSTRUCTOR_TABLE),
                    'delete' => Yii::$app->user->can(RegistryModule::CAN_DELETE_CONSTRUCTOR_TABLE),
                ],
            ]),
        ],
    ]); ?>
</div>
