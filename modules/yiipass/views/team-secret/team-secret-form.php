<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\BaseHtml;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
?>

<?php $form = ActiveForm::begin() ?>

    <?= $form->field($model, 'team_secret')->textInput(['maxlength' => true]) ?>

    <button>Submit</button>

<?php ActiveForm::end(); ?>
