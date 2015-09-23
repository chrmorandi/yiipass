<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\yiipass\services\CustomGridViewService;
use yii\helpers\ArrayHelper;
use app\models\Password;

use app\modules\yiipass\assets\YiiPassAsset;
YiiPassAsset::register($this);

/* @var $this yii\web\View */
/* @var $searchModel app\models\PasswordSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Account Credentials Overview';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="password-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if(Yii::$app->session->hasFlash('success')){ ?>
        <div class="alert alert-success" role="alert">
            <?= Yii::$app->session->getFlash('success'); ?>
        </div>
    <?php } ?>

    <div class="row">
        <div class="col-xs-12">
            <nav class="navbar">
                <div class="dropdown">
                    <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                        Actions
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                        <li><?= Html::a('<span class="glyphicon glyphicon-plus"></span> ' . \Yii::t('app', 'Add Password'), ['create']) ?></li>
                        <li><?= Html::a('<span class="glyphicon glyphicon-upload"></span> ' . \Yii::t('app', 'Upload KeePass XML'), ['upload-new-xml']) ?></li>
                        <li><?= Html::a('<span class="glyphicon glyphicon-download"></span> ' . \Yii::t('app', 'Download KeePass XML'), ['download-passwords-as-kee-pass-xml']) ?></li>
                    </ul>
                </div>
            </nav>
        </div>
    </div>

    <?php
    $arr_widget = [
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'title',
            'group',
            'lastaccess',

            ['class' => 'yii\grid\ActionColumn',
             'template' => '{update} {open_url} {copy_username} {copy_password}',
             'buttons' => [
                 'open_url' => function($url, $model, $key){
                     if($model->url !== ''){
                         return '<a href="' . $model->url . '" title="Open URL in new window" target="_blank">Open URL</a>';
                     }
                 },
                 'copy_username' => function($url, $model, $key){
                     if($model->username !== ''){
                         return '<button type="button" class="copy_username copy_button" data-clipboard-text="' . $model->username . '" title="Click to copy me.">Copy Username</button>';
                     }
                 },
                 'copy_password' => function($url, $model, $key){
                     if($model->password !== ''){
                         return '<button type="button" class="copy_password copy_button" data-clipboard-text="' . $model->password . '" title="Click to copy me.">Copy Password</button>';
                     }
                 }
             ]
            ],
        ],
    ];

    // If user is admin, set checkbox column at the beginning of the columns.
    if (Yii::$app->user->identity->is_admin){
        /**
         * If checkboxes will be needed, comment the following line in.
         * Increase then the array key of columns.
         */
        // array_unshift($arr_widget['columns'], ['class' => 'yii\grid\CheckboxColumn']);
        $arr_widget['columns']['3']['template'] .= '{delete} ';
    }

    ?>

    <?= CustomGridViewService::widget($arr_widget); ?>

</div>
