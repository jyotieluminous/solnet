<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Linkcustomepackage */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="linkcustomepackage-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'fk_customer_id')->textInput() ?>

    <?= $form->field($model, 'fk_package_id')->textInput() ?>

    <?= $form->field($model, 'fk_speed_id')->textInput() ?>

    <?= $form->field($model, 'fk_currency_id')->textInput() ?>

    <?= $form->field($model, 'package_speed')->textInput() ?>

    <?= $form->field($model, 'package_price')->textInput() ?>

    <?= $form->field($model, 'other_service_fee')->textInput() ?>

    <?= $form->field($model, 'installation_fee')->textInput() ?>

    <?= $form->field($model, 'payment_type')->dropDownList([ 'term' => 'Term', 'advance' => 'Advance', 'bulk' => 'Bulk', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'payment_term')->textInput() ?>

    <?= $form->field($model, 'bulk_pay_start')->textInput() ?>

    <?= $form->field($model, 'bulk_pay_end')->textInput() ?>

    <?= $form->field($model, 'installation_address')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'order_received_date')->textInput() ?>

    <?= $form->field($model, 'activation_date')->textInput() ?>

    <?= $form->field($model, 'contract_start_date')->textInput() ?>

    <?= $form->field($model, 'contract_end_date')->textInput() ?>

    <?= $form->field($model, 'invoice_start_date')->textInput() ?>

    <?= $form->field($model, 'is_solnet_bank')->dropDownList([ 'yes' => 'Yes', 'no' => 'No', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'bank_id')->textInput() ?>

    <?= $form->field($model, 'bank_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'virtual_acc_no')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'account_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'contract_number')->textInput() ?>

    <?= $form->field($model, 'is_disconnected')->dropDownList([ 'yes' => 'Yes', 'no' => 'No', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'disconnection_date')->textInput() ?>

    <?= $form->field($model, 'reactivation_date')->textInput() ?>

    <?= $form->field($model, 'is_current_package')->dropDownList([ 'yes' => 'Yes', 'no' => 'No', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'contract_status')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
