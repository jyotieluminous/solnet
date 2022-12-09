<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\EmailLogsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="email-logs-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'email_log_id') ?>

    <?= $form->field($model, 'email_to') ?>

    <?= $form->field($model, 'subject') ?>

    <?= $form->field($model, 'is_customer') ?>

    <?= $form->field($model, 'is_user') ?>

    <?php // echo $form->field($model, 'sent_to_id') ?>

    <?php // echo $form->field($model, 'sent_by') ?>

    <?php // echo $form->field($model, 'sent_by_user_id') ?>

    <?php // echo $form->field($model, 'sent_date') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
