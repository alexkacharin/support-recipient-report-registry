<?php
    return [
        '/' => 'site/index',
        'settings/home' => 'frontend-settings/index',
        'settings/home/<id:\d+>' => 'frontend-settings/view',
        'settings/home/<action:[\w-]+>/<id:\d+>' => 'frontend-settings/<action>',
        'settings/home/<action:[\w-]+>' => 'frontend-settings/<action>',

        '<controller:[\w-]+>' => '<controller>/index',
        '<controller:[\w-]+>/<id:\d+>' => '<controller>/view',
        '<controller:[\w-]+>/<action:[\w-]+>/<id:\d+>' => '<controller>/<action>',
        '<controller:[\w-]+>/<action:[\w-]+>' => '<controller>/<action>',
    ];
?>
