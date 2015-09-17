<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Password */

$this->title = 'Update Password: ' . ' ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Passwords', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="password-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php
    $elements_to_render['model'] = $model;
    ?>
    <?=
    $this->render('_form', [
        'model' => $model,
        'user_checkboxes' => isset($user_checkboxes) ? $user_checkboxes : false
    ]) ?>

</div>
