<?php

namespace Matodor\RegistryConstructor\widgets\RecordsGrid\assets;

use Matodor\RegistryConstructor\assets\StoreJsAssets;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class ResizableGridAssets extends AssetBundle
{
    public $sourcePath = __DIR__ . '/../web';

    public $css = [
        'css/resizable.css',
    ];

    public $js = [
        'js/resizable.js',
    ];

    public $depends = [
        JqueryAsset::class,
        StoreJsAssets::class,
    ];
}
