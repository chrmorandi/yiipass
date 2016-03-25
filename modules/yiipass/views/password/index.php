<?php

use yii\helpers\Html;
use app\modules\yiipass\services\CustomGridViewService;
use app\models\Password;
use \app\modules\yiipass\controllers\PasswordController;

use app\modules\yiipass\assets\YiiPassAsset;

YiiPassAsset::register($this);

/* @var $this yii\web\View */
/* @var $searchModel app\models\PasswordSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = 'Account Credentials Overview';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="password-index">

    <h1><?= Html::encode($this->title) ?></h1>

  <!-- Modal -->
  <div id="mobileCopyModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Modal Header</h4>
        </div>
        <div class="modal-body">
          <form>
            <span id="username_field">
                Username:<br/>
                <input type="text" name="password" id="username" />
                <button type="button" class="btn btn-default" id="select-username">Select</button>
                <br/>
            </span>
            <span id="password_field">
                Password:<br/>
                <input type="text" name="password" id="password" />
                <button type="button" class="btn btn-default" id="select-password">Select</button>
            </span>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>

    </div>
  </div>
  <!-- Modal end -->


  <?php if (Yii::$app->session->hasFlash('success')) { ?>
      <div class="alert alert-success" role="alert">
          <?= Yii::$app->session->getFlash('success'); ?>
      </div>
  <?php } ?>

  <div class="row">
      <div class="col-xs-12">
          <nav class="navbar">
              <div class="dropdown">
                  <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1"
                          data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
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
      'filterModel'  => $searchModel,
      'columns'      => [
          ['class'  => 'yii\grid\DataColumn',
           'attribute' => 'title',
           'format' => 'html',
           'value'  => function (\app\modules\yiipass\models\Password $password) {
                          return Html::a($password->title, ['view?id=' . $password->id]);
                      }
          ],
          'group',
          ['class'    => 'yii\grid\ActionColumn',
           'template' => '{open_url} {copy}',
           'buttons'  => [
               'open_url'      => function ($url, $model, $key) {
                                       if ($model->url !== '') {
                                           return '<a href="' . $model->url . '" title="Open URL in new window" target="_blank">Open URL</a>';
                                       }
                                   },
               'copy' => function ($url, $model, $key) {
                 return '<button type="button" class="copy_username copy_button"
                                          data-toggle="modal" data-target="#mobileCopyModal"
                                          data-username="' . $model->username . '"
                                          data-password="' . PasswordController::decrypt($model->password) . '"
                                          data-title="' . $model->title . '"
                                          title="Copy details">Copy</button>';
               }
           ]
          ],
          ['class'    => 'yii\grid\ActionColumn',
           'template' => '{update}',
          ],
          ['class'    => 'yii\grid\ActionColumn',
           'template' => '{delete}',
          ],
      ],
  ];

  ?>

  <?= CustomGridViewService::widget($arr_widget); ?>

</div>
