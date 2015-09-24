<?php
/**
 * Created by Peter Majmesku.
 * E-Mail: p.majmesku@gmail.com
 * Date: 27.07.15
 * Time: 00:21
 */

$this->title = 'Upload new XML';
$this->params['breadcrumbs'][] = ['label' => 'Passwords', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?php
use yii\widgets\ActiveForm;
?>

<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

<?= $form->field($model, 'file')->fileInput() ?>

    <button>Submit</button>

<?php ActiveForm::end() ?>