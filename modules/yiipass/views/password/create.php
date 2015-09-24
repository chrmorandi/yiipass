<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Password */

$this->title = 'Create Password';
$this->params['breadcrumbs'][] = ['label' => 'Passwords', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="password-create">

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
