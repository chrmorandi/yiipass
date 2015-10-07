<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\BaseHtml;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
?>

<?php if (Yii::$app->session->hasFlash('error')) { ?>
    <div class="alert alert-danger" role="alert">
        <?= Yii::$app->session->getFlash('error'); ?>
    </div>
<?php } ?>

<?php if (Yii::$app->session->hasFlash('info')) { ?>
    <div class="alert alert-info" role="info">
        <?= Yii::$app->session->getFlash('info'); ?>
    </div>
<?php } ?>

<?php $form = ActiveForm::begin(['id' => 'team-secret-form']) ?>

    <?= $form->field($model, 'teamSecret')->textInput(['maxlength' => true]) ?>

    <button>Submit</button>

<?php ActiveForm::end(); ?>
