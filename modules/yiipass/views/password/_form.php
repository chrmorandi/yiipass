<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\BaseHtml;

/* @var $this yii\web\View */
/* @var $model app\models\Password */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="password-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= Html::button('Generate Password', array('id' => 'generate_password')) ?>

    <p>
        <?= BaseHtml::hiddenInput('Password[id]', $model->id) ?>

        <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'group')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'password')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'comment')->textarea(['rows' => 6]) ?>

        <?= $form->field($model, 'url')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'expire')->textInput() ?>
    </p>

    <?php // Will be displayed only, if user is admin. ?>
    <?php echo $user_checkboxes ?: $user_checkboxes; ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
