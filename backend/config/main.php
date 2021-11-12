<?php

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'modules' => [
        \Matodor\RegistryConstructor\Module::MODULE_NAME => [
            'urlPrefix' => '',
            'urlRules' => [
                'tables/<tableId:\d+>' => 'records/index',
                'tables/<tableId:\d+>/<action:[\w-]+>/<recordId:\d+>' => 'records/<action>',
                'tables/<tableId:\d+>/<action:[\w-]+>' =>'records/<action>',

                'constructor' => 'table-constructor/index',
                'constructor/<id:\d+>' => 'table-constructor/view',
                'constructor/<action:[\w-]+>/<id:\d+>' => 'table-constructor/<action>',
                'constructor/<action:[\w-]+>' => 'table-constructor/<action>',
            ],
        ],
    ],
    'components' => [
        'view' => [
            'params' => [
                'containerClass' => 'container-fluid',
            ],
        ],
        'request' => [
            'csrfParam' => '_csrf-backend',
            'baseUrl' => '/admin',
        ],
        'user' => [
            'enableAutoLogin' => true,
            'identityCookie' => [
                'name'     => '_identity',
                'path'     => '/',
                'httpOnly' => true,
                'domain' => '.' . CURRENT_DOMAIN,
            ],
        ],
        'session' => [
            'name' => 'site-session',
            'cookieParams' => [
                'lifetime' => 2 * 24 * 3600,
                'httpOnly' => true,
                'path'     => '/',
                'domain' => '.' . CURRENT_DOMAIN,
            ],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => array_merge(
                require __DIR__ . '/rules.php'
            ),
        ],
    ],
    'params' => $params,
    'on beforeRequest' => function () {
        if (!Yii::$app->user->isGuest
            && !Yii::$app->user->can('has_backend_access')
        ) {
            $user = Yii::$app->user->identity;
            if ($user instanceof \dektrium\user\models\User
                && !$user->isAdmin
            ) {
                Yii::$app->session->addFlash('success', 'Доступ запрещен');
                Yii::$app->end(403, Yii::$app->response->redirect(\Matodor\Common\components\Helper::to(
                    ['/site/index'], 'frontend', true)));
            }
        }
    },
];
