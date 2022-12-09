<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\OutstandingRemarkssearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="outstanding-remarks-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'outstanding_remark_id') ?>

    <?= $form->field($model, 'fk_customer_id') ?>

    <?= $form->field($model, 'fk_invoice_id') ?>

    <?= $form->field($model, 'remark1') ?>

    <?= $form->field($model, 'remark2') ?>

    <?php // echo $form->field($model, 'fk_user_id') ?>

    <?php // echo $form->field($model, 'created_date') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
