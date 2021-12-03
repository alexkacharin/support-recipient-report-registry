<?php

namespace backend\assets;

use common\assets\CommonAssets;
use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        'css/base.css',
        'css/site.css',
    ];

    public $js = [
        'js/owl.carousel.min.js',
    ];

    public $depends = [
        CommonAssets::class,
    ];
}
