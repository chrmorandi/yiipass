<?php

namespace app\modules\yiipass\assets;

use yii\web\AssetBundle;

class YiiPassAsset extends AssetBundle
{
    public $sourcePath = '@yiipass-assets';
    public $js = [
        'js/ZeroClipboard/ZeroClipboard.min.js',
        'js/MainDesktop.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
