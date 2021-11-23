<?php

namespace Matodor\RegistryConstructor\assets;

use yii\web\AssetBundle;

class StoreJsAssets extends AssetBundle
{
    public $sourcePath = '@npm/store/dist';

    public $js = [
        'store.modern.min.js',
    ];
}
