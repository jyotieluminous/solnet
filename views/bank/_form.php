<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Currency;
use yii\helpers\ArrayHelper;
/* @var $this yii\web\View */
/* @var $model app\models\Bank */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="box box-default">
  <div class="box-body">
    <div class="row">
        <div class="bank-form">
            <?php $form = ActiveForm::begin(); ?>
            <div class="form-group required col-md-6">
                <?php echo $form->field($model, 'bank_name')->textInput(['maxlength' => true]) ?>

                <?php echo $form->field($model, 'account_no')->textInput(['maxlength' => true]) ?>

                <?php echo $form->field($model, 'account_name')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="form-group required col-md-6">
                <?php echo $form->field($model, 'bank_branch')->textInput(['maxlength' => true]) ?>   
                <?php 

                    $arrCurrency=Currency::find()->all();

                    $listCurrency=ArrayHelper::map($arrCurrency,'currency_id','currency');

                    echo $form->field($model, 'fk_currency_id')->dropDownList($listCurrency)->label('Currency');
                 ?>  

                <?php
                if($flagShowStatus=='1'){

                    echo $form->field($model, 'status')->radioList(array('active'=>'Active','inactive'=>'Inactive'));
                 }else{
                                   
                    echo $form->field($model, 'status')->radioList(
                     array('active'=>'Active'));
            
                    } ?>
            </div>
         </div>
        </div>
        <div class="form-group">
            <?php echo Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
            <?php echo Html::a('Cancel',['index'], ['class' => 'btn btn-warning']) ?>
        </div>
           
            <?php ActiveForm::end(); ?>
     </div>
    </div>
 </div>
</div>
