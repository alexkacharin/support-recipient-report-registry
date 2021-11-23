<?php

namespace Matodor\RegistryConstructor\widgets\RecordsGrid\assets;

use yii\web\AssetBundle;

class BootstrapTableAssets extends AssetBundle
{
    public $sourcePath = '@npm/bootstrap-table/dist';

    public $css = [
        'bootstrap-table.css',
        'extensions/sticky-header/bootstrap-table-sticky-header.css',
    ];

    public $js = [
        'bootstrap-table.js',
        'locale/bootstrap-table-ru-RU.js',
        'extensions/sticky-header/bootstrap-table-sticky-header.js',
    ];
}
