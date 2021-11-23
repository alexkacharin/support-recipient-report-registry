<?php

namespace Matodor\RegistryConstructor\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class SortableAssets extends AssetBundle
{
    public $sourcePath = __DIR__ . '/../web';

    public $js = [
        'js/Sortable.min.js',
        'js/jquery-sortable.js',
    ];

    public $depends = [
        JqueryAsset::class,
    ];
}
