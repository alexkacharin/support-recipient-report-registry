<?php

namespace Matodor\RegistryConstructor\widgets\RecordsGrid\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class WidgetAssets extends AssetBundle
{
    public $sourcePath = __DIR__ . '/../web';

    public $css = [
        'css/widget.css',
    ];

    public $depends = [
        BootstrapTableAssets::class,
        JqueryAsset::class,
    ];
}
