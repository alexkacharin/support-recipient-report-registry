<?php

/** @noinspection MissedFieldInspection */

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'modules' => [
        'user' => [
            'enableConfirmation' => true,
            'enableRegistration' => true,
            'enableGeneratingPassword' => true,
            'controllerMap' => [
                'registration' => \frontend\controllers\RegistrationController::class,
            ],
        ],
        \Matodor\RegistryConstructor\Module::MODULE_NAME => [
            'urlPrefix' => '',
            'urlRules' => [
                'tables/<tableId:\d+>' => 'records/index',
                'tables/<tableId:\d+>/<action:[\w-]+>/<recordId:\d+>' => 'records/<action>',
                'tables/<tableId:\d+>/<action:[\w-]+>' =>'records/<action>',
            ],
            'as frontendFilter' => \Matodor\RegistryConstructor\filters\FrontendAccessFilter::class,
        ],
    ],
    'components' => [
        'view' => [
            'params' => [
                'containerClass' => 'container',
            ],
        ],
        'request' => [
            'csrfParam' => '_csrf-frontend',
            'baseUrl' => '',
            'csrfCookie' => [
                'domain' => '.' . CURRENT_DOMAIN
            ],
        ],
        'user' => [
            'enableAutoLogin' => true,
            'identityCookie' => [
                'name'     => '_identity',
                'httpOnly' => true,
                'path'     => '/',
                'domain' => '.' . CURRENT_DOMAIN
            ],
        ],
        'session' => [
            'name' => 'site-session',
            'cookieParams' => [
                'lifetime' => 2 * 24 * 3600,
                'httpOnly' => true,
                'path'     => '/',
                'domain' => '.' . CURRENT_DOMAIN
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
            )
        ],
		'assetManager' => [
            'linkAssets' => false
        ],
    ],
    'params' => $params,
];
