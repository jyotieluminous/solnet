<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\LinkcustomepackageSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="linkcustomepackage-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'cust_pck_id') ?>

    <?= $form->field($model, 'fk_customer_id') ?>

    <?= $form->field($model, 'fk_package_id') ?>

    <?= $form->field($model, 'fk_speed_id') ?>

    <?= $form->field($model, 'fk_currency_id') ?>

    <?php // echo $form->field($model, 'package_speed') ?>

    <?php // echo $form->field($model, 'package_price') ?>

    <?php // echo $form->field($model, 'other_service_fee') ?>

    <?php // echo $form->field($model, 'installation_fee') ?>

    <?php // echo $form->field($model, 'payment_type') ?>

    <?php // echo $form->field($model, 'payment_term') ?>

    <?php // echo $form->field($model, 'bulk_pay_start') ?>

    <?php // echo $form->field($model, 'bulk_pay_end') ?>

    <?php // echo $form->field($model, 'installation_address') ?>

    <?php // echo $form->field($model, 'order_received_date') ?>

    <?php // echo $form->field($model, 'activation_date') ?>

    <?php // echo $form->field($model, 'contract_start_date') ?>

    <?php // echo $form->field($model, 'contract_end_date') ?>

    <?php // echo $form->field($model, 'invoice_start_date') ?>

    <?php // echo $form->field($model, 'is_solnet_bank') ?>

    <?php // echo $form->field($model, 'bank_id') ?>

    <?php // echo $form->field($model, 'bank_name') ?>

    <?php // echo $form->field($model, 'virtual_acc_no') ?>

    <?php // echo $form->field($model, 'account_name') ?>

    <?php // echo $form->field($model, 'contract_number') ?>

    <?php // echo $form->field($model, 'is_disconnected') ?>

    <?php // echo $form->field($model, 'disconnection_date') ?>

    <?php // echo $form->field($model, 'reactivation_date') ?>

    <?php // echo $form->field($model, 'is_current_package') ?>

    <?php // echo $form->field($model, 'contract_status') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
