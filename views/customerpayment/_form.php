<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Customerpayment */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="customerpayment-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'fk_customer_id')->textInput() ?>

    <?= $form->field($model, 'fk_invoice_id')->textInput() ?>

    <?= $form->field($model, 'discount')->textInput() ?>

    <?= $form->field($model, 'deduct_tax')->textInput() ?>

    <?= $form->field($model, 'bank_admin')->textInput() ?>

    <?= $form->field($model, 'payment_method')->dropDownList([ 'cash' => 'Cash', 'virtual_transfer' => 'Virtual transfer', 'bank' => 'Bank', 'cheque' => 'Cheque', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'cheque_no')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'amount_paid')->textInput() ?>

    <?= $form->field($model, 'payment_date')->textInput() ?>

    <?= $form->field($model, 'reciept_no')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'comment')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
