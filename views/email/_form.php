<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\EmailLogs */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="email-logs-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'email_to')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'subject')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'is_customer')->dropDownList([ 'Yes' => 'Yes', 'No' => 'No', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'is_user')->dropDownList([ 'Yes' => 'Yes', 'No' => 'No', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'sent_to_id')->textInput() ?>

    <?= $form->field($model, 'sent_by')->dropDownList([ 'System' => 'System', 'User' => 'User', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'sent_by_user_id')->textInput() ?>

    <?= $form->field($model, 'sent_date')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
