<?php
use yii\helpers\Html;
?>
<li class="list-group-item"> <?= Html::checkbox("allowed_users[$username]", $checked, ['value' => $user_id]) ?>
                                <?= $username; ?></li>