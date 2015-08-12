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

$this->title = 'Passwords';
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

    <?= CustomGridViewService::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\CheckboxColumn'],

            'title',
            'group',
            'lastaccess',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
