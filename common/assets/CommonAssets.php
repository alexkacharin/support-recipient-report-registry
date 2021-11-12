<?php

namespace common\assets;

use Yii;
use yii\web\AssetBundle;

class CommonAssets extends AssetBundle
{
    public $css = [
        'css/font-awesome.min.css',
        'css/common.css',
    ];

    public $js = [
        'js/common.js',
    ];

    public $depends = [
        \yii\web\YiiAsset::class,
        \yii\bootstrap4\BootstrapPluginAsset::class,
        \Matodor\Common\assets\CommonAssets::class,
    ];

    public function init()
    {
        parent::init();

        $this->sourcePath = Yii::getAlias('@common/web');
    }
}
