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

    <?= $this->render('_form', [
        'model' => $model,
        'all_users' => $all_users,
        'user_model' => $user_model,
        'users_account_credential_ids' => $users_account_credential_ids
    ]) ?>

</div>
