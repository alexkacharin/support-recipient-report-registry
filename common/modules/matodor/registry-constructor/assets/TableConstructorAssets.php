<?php

namespace Matodor\RegistryConstructor\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class TableConstructorAssets extends AssetBundle
{
    public $sourcePath = __DIR__ . '/../web';

    public $css = [
        'css/table-crud.css',
    ];

    public $js = [
        'js/table-crud.js',
    ];

    public $depends = [
        JqueryAsset::class,
    ];
}
