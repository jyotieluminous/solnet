<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\CustomerinvoiceSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="customerinvoice-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'customer_invoice_id') ?>

    <?= $form->field($model, 'invoice_number') ?>

    <?= $form->field($model, 'fk_customer_id') ?>

    <?= $form->field($model, 'invoice_type') ?>

    <?= $form->field($model, 'invoice_date') ?>

    <?php // echo $form->field($model, 'usage_period_from') ?>

    <?php // echo $form->field($model, 'usage_period_to') ?>

    <?php // echo $form->field($model, 'due_date') ?>

    <?php // echo $form->field($model, 'fk_cust_pckg_id') ?>

    <?php // echo $form->field($model, 'last_due_invoice_id') ?>

    <?php // echo $form->field($model, 'last_due_amount') ?>

    <?php // echo $form->field($model, 'last_invoice_date') ?>

    <?php // echo $form->field($model, 'current_invoice_amount') ?>

    <?php // echo $form->field($model, 'installation_fee') ?>

    <?php // echo $form->field($model, 'vat') ?>

    <?php // echo $form->field($model, 'other_service_fee') ?>

    <?php // echo $form->field($model, 'total_invoice_amount') ?>

    <?php // echo $form->field($model, 'deduct_tax') ?>

    <?php // echo $form->field($model, 'discount') ?>

    <?php // echo $form->field($model, 'bank_amount') ?>

    <?php // echo $form->field($model, 'paid_amount') ?>

    <?php // echo $form->field($model, 'pending_amount') ?>

    <?php // echo $form->field($model, 'payment_method') ?>

    <?php // echo $form->field($model, 'next_invoice_date') ?>

    <?php // echo $form->field($model, 'next_usage_date_from') ?>

    <?php // echo $form->field($model, 'comments') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'is_mail_sent') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
