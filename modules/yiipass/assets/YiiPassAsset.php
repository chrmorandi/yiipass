<?php

namespace app\modules\yiipass\assets;

use yii\web\AssetBundle;

class YiiPassAsset extends AssetBundle
{
    public $sourcePath = '@yiipass-assets';
    public $js = [
        'js/MenuClickDispatcher.js',
        'js/ZeroClipboard/ZeroClipboard.min.js',
        'js/Main.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}