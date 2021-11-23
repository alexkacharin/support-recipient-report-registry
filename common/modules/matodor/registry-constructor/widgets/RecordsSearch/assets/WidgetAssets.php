<?php

namespace Matodor\RegistryConstructor\widgets\RecordsSearch\assets;

use yii\web\AssetBundle;
use yii\web\YiiAsset;

class WidgetAssets extends AssetBundle
{
    public $sourcePath = __DIR__ . '/../web';

    public $css = [
        'css/widget.css',
    ];

    public $js = [
        'js/widget.js',
    ];

    public $depends = [
        YiiAsset::class,
    ];
}
