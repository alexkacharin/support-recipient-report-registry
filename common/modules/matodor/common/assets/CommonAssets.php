<?php

namespace Matodor\Common\assets;

use yii\web\AssetBundle;

class CommonAssets extends AssetBundle
{
    public $sourcePath = __DIR__ . '/../web';

    public $js = [
        'js/common.js',
    ];
}
