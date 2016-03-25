<?php

namespace app\modules\yiipass\assets;

use yii\web\AssetBundle;

class YiiPassMobileAsset extends AssetBundle
{
    public $sourcePath = '@yiipass-assets';
    public $js = [
        'js/Main.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
