<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\OutstandingRemarks */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="outstanding-remarks-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'fk_customer_id')->textInput() ?>

    <?= $form->field($model, 'fk_invoice_id')->textInput() ?>

    <?= $form->field($model, 'remark1')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'remark2')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'fk_user_id')->textInput() ?>

    <?= $form->field($model, 'created_date')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
