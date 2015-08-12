<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Password */
/* @var $form ActiveForm */
?>
<div class="edit">

    <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'title') ?>
        <?= $form->field($model, 'password') ?>
        <?= $form->field($model, 'group') ?>
        <?= $form->field($model, 'comment') ?>
        <?= $form->field($model, 'creation') ?>
        <?= $form->field($model, 'lastaccess') ?>
        <?= $form->field($model, 'lastmod') ?>
        <?= $form->field($model, 'expire') ?>
        <?= $form->field($model, 'username') ?>
        <?= $form->field($model, 'url') ?>
    
        <div class="form-group">
            <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>

</div><!-- edit -->
