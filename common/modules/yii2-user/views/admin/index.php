<?php

use dektrium\user\models\User;
use dektrium\user\models\UserSearch;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Pjax;

/** @var View $this */
/** @var ActiveDataProvider $dataProvider */
/** @var UserSearch $searchModel */

$this->title = Yii::t('user', 'Manage users');
$this->params['breadcrumbs'][] = $this->title;

?>

<?= $this->render('/_alert', ['module' => Yii::$app->getModule('user')]) ?>
<?= $this->render('/admin/_menu') ?>

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
                        'attribute' => 'id',
                        'headerOptions' => ['style' => 'width:90px;'], # 90px is sufficient for 5-digit user ids
                    ],
                    'username',
                    'email:email',
                    [
                        'attribute' => 'registration_ip',
                        'format' => 'html',
                        'value' => function ($model) {
                            /** @var User $model */

                            return $model->registration_ip == null
                                ? '<span class="not-set">' . Yii::t('user', '(not set)') . '</span>'
                                : $model->registration_ip;
                        },
                    ],
                    [
                        'attribute' => 'created_at',
                        'value' => function ($model) {
                            /** @var User $model */

                            if (extension_loaded('intl')) {
                                return Yii::t('user', '{0, date, MMMM dd, YYYY HH:mm}', [$model->created_at]);
                            } else {
                                return date('Y-m-d G:i:s', $model->created_at);
                            }
                        },
                    ],

                    [
                        'attribute' => 'last_login_at',
                        'value' => function ($model) {
                            /** @var User $model */

                            if (!$model->last_login_at || $model->last_login_at == 0) {
                                return Yii::t('user', 'Never');
                            } else if (extension_loaded('intl')) {
                                return Yii::t('user', '{0, date, MMMM dd, YYYY HH:mm}', [$model->last_login_at]);
                            } else {
                                return date('Y-m-d G:i:s', $model->last_login_at);
                            }
                        },
                    ],
                    [
                        'header' => Yii::t('user', 'Confirmation'),
                        'format' => 'raw',
                        'visible' => Yii::$app->getModule('user')->enableConfirmation,
                        'value' => function ($model) {
                            /** @var User $model */

                            if ($model->isConfirmed) {
                                return '<div class="text-center">
                                            <span class="text-success">' . Yii::t('user', 'Confirmed') . '</span>
                                        </div>';
                            } else {
                                return Html::a(Yii::t('user', 'Confirm'), ['confirm', 'id' => $model->id], [
                                    'class' => 'btn btn-xs btn-success btn-block',
                                    'data-method' => 'post',
                                    'data-confirm' => Yii::t('user', 'Are you sure you want to confirm this user?'),
                                ]);
                            }
                        },
                    ],
                    [
                        'header' => Yii::t('user', 'Block status'),
                        'format' => 'raw',
                        'value' => function ($model) {
                            /** @var User $model */

                            if ($model->isBlocked) {
                                return Html::a(Yii::t('user', 'Unblock'), ['block', 'id' => $model->id], [
                                    'class' => 'btn btn-xs btn-success btn-block',
                                    'data-method' => 'post',
                                    'data-confirm' => Yii::t('user', 'Are you sure you want to unblock this user?'),
                                ]);
                            } else {
                                return Html::a(Yii::t('user', 'Block'), ['block', 'id' => $model->id], [
                                    'class' => 'btn btn-xs btn-danger btn-block',
                                    'data-method' => 'post',
                                    'data-confirm' => Yii::t('user', 'Are you sure you want to block this user?'),
                                ]);
                            }
                        },
                    ],

                    [
                        'class' => \yii\grid\ActionColumn::class,
                        'template' => '<div class="m-n1">{switch}{resend_password}{update}{delete}</div>',
                        'options' => ['style' => 'width: 95px;'],
                        'buttons' => [
                            'update' => function ($url, $model) {
                                /** @var User $model */

                                return Html::a('<i class="fa fa-edit"></i>', $url, [
                                    'class' => 'btn btn-sm btn-primary m-1',
                                    'target' => '_blank',
                                ]);
                            },
                            'delete' => function ($url, $model) {
                                /** @var User $model */

                                return Html::a('<i class="fa fa-trash"></i>', $url, [
                                    'class' => 'btn btn-sm btn-danger m-1',
                                    'data' => [
                                        'confirm' => Yii::t('user', 'Are you sure?'),
                                        'method' => 'post',
                                    ],
                                ]);
                            },
                            'resend_password' => function ($url, $model, $key) {
                                /** @var User $model */

                                if (Yii::$app->user->identity->isAdmin
                                    && !$model->isAdmin
                                ) {
                                    return Html::a('<i class="fas fa-envelope"></i>', [
                                        'resend-password',
                                        'id' => $model->id,
                                    ], [
                                        'title' => Yii::t('user', 'Generate and send new password to user'),
                                        'class' => 'btn btn-sm btn-info m-1',
                                        'data' => [
                                            'confirm' => Yii::t('user', 'Are you sure?'),
                                            'method' => 'POST',
                                        ],
                                    ]);
                                }

                                return '';
                            },
                            'switch' => function ($url, $model) {
                                /** @var User $model */

                                if (Yii::$app->user->identity->isAdmin
                                    && $model->id != Yii::$app->user->id
                                    && Yii::$app->getModule('user')->enableImpersonateUser
                                ) {
                                    $url = [
                                        '/user/admin/switch',
                                        'id' => $model->id,
                                    ];

                                    return Html::a('<i class="fas fa-user"></i>', $url, [
                                        'class' => 'btn btn-sm btn-primary m-1',
                                        'title' => Yii::t('user', 'Become this user'),
                                        'data-confirm' => Yii::t('user', 'Are you sure you want to switch to this user for the rest of this Session?'),
                                        'data-method' => 'POST',
                                    ]);
                                }

                                return '';
                            },
                        ],
                    ],
                ],
            ]); ?>
        </div>
    </div>
<?php Pjax::end() ?>
