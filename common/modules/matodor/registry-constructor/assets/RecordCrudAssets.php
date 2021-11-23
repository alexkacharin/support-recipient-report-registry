<?php

namespace Matodor\RegistryConstructor\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class RecordCrudAssets extends AssetBundle
{
    public $sourcePath = __DIR__ . '/../web';

    public $css = [
        'css/record-crud.css',
    ];

    public $js = [
        'js/record-crud.js',
    ];

    public $depends = [
        JqueryAsset::class,
    ];
}
