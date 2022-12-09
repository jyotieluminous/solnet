<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ProspectSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="prospect-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'prospect_id') ?>

    <?= $form->field($model, 'customer_name') ?>

    <?= $form->field($model, 'person_incharge') ?>

    <?= $form->field($model, 'address') ?>

    <?= $form->field($model, 'mobile_no') ?>

    <?php // echo $form->field($model, 'email') ?>

    <?php // echo $form->field($model, 'current_isp') ?>

    <?php // echo $form->field($model, 'current_contract_end_date') ?>

    <?php // echo $form->field($model, 'current_package') ?>

    <?php // echo $form->field($model, 'current_isp_bill') ?>

    <?php // echo $form->field($model, 'fk_currency_id') ?>

    <?php // echo $form->field($model, 'fk_package_id') ?>

    <?php // echo $form->field($model, 'fk_speed_id') ?>

    <?php // echo $form->field($model, 'package_speed') ?>

    <?php // echo $form->field($model, 'price_quote') ?>

    <?php // echo $form->field($model, 'estimate_sign_up_date') ?>

    <?php // echo $form->field($model, 'success_rate') ?>

    <?php // echo $form->field($model, 'is_deal_closed') ?>

    <?php // echo $form->field($model, 'fk_user_id') ?>

    <?php // echo $form->field($model, 'is_deleted') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
