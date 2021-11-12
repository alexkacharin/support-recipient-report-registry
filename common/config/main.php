<?php

return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'language' => 'ru-RU',
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'bootstrap' => [
        \dektrium\user\Bootstrap::class,
        \dektrium\rbac\Bootstrap::class,
        \Matodor\RegistryConstructor\Bootstrap::class,
    ],
    'container' => [
        'definitions' => [],
    ],
    'modules' => [
        \Matodor\RegistryConstructor\Module::MODULE_NAME => [
            'class' => \Matodor\RegistryConstructor\Module::class ,
            'storagePath' => '@backend/runtime/uploads',
        ],
        'rbac' => [
            'class' => \dektrium\rbac\RbacWebModule::class,
        ],
        'user' => [
            'class' => \dektrium\user\Module::class,
            'admins' => ['superadmin'],
            'enableConfirmation' => false,
            'enableRegistration' => false,
            'enableFlashMessages' => false,
            'modelMap' => [
                'LoginForm' => \common\models\forms\LoginForm::class,
                'RegistrationForm' => \common\models\forms\RegistrationForm::class,
                'User' => \common\models\User::class,
            ],
            'mailer' => [
                'viewPath' => '@common/mail',
            ],
        ],
    ],
    'components' => [
        'view' => [
            'theme' => [
                'pathMap' => [
                    '@dektrium/user/views/security' => '@common/views/user/security',
                    '@dektrium/user/views/registration' => '@common/views/user/registration',
                    '@dektrium/user/views/mail' => '@common/views/user/mail',
                ],
            ],
        ],
        'log' => [
            'flushInterval' => 100,
        ],
        'assetManager' => [
            'linkAssets' => YII_ENV_DEV,
            'appendTimestamp' => YII_ENV_PROD,
            'bundles' => [
                'yii\bootstrap\BootstrapAsset' => false,
                'yii\bootstrap\BootstrapPluginAsset' => false,
            ],
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
    ],
];
