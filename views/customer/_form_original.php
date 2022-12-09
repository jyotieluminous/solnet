<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Customer */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="customer-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'solnet_customer_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fk_country_id')->textInput() ?>

    <?= $form->field($model, 'fk_state_id')->textInput() ?>

    <?= $form->field($model, 'ktp_pass_no')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'billing_address')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'user_type')->dropDownList([ 'home' => 'Home', 'corporate' => 'Corporate', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'email_address')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email_finance')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email_it')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'mobile_no')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fixed_line_no')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'filepath')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'installation_status')->dropDownList([ 'yes' => 'Yes', 'no' => 'No', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'is_invoice_activated')->dropDownList([ 'yes' => 'Yes', 'no' => 'No', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'invoice_send_via')->dropDownList([ 'email' => 'Email', 'hardcopy' => 'Hardcopy', 'both' => 'Both', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'is_address_same')->dropDownList([ 'yes' => 'Yes', 'no' => 'No', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'first_invoice_date')->textInput() ?>

    <?= $form->field($model, 'last_package_change_date')->textInput() ?>

    <?= $form->field($model, 'fk_user_id')->textInput() ?>

    <?= $form->field($model, 'status')->dropDownList([ 'active' => 'Active', 'inactive' => 'Inactive', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'is_deleted')->dropDownList([ '0', '1', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
