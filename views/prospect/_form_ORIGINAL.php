<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use dosamigos\datepicker\DatePicker;
use app\models\Currency;
use app\models\Speed;
use app\models\Package;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\Prospect */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="box box-default">
    <div class="box-body">
        <div class="row">
            <div class="prospect-form">
                
                <?php $form = ActiveForm::begin(); ?>

                <div class="col-md-6">

                    <?php echo $form->field($model, 'customer_name')->textInput(['maxlength' => true]) ?>

                    <?php echo $form->field($model, 'person_incharge')->textInput(['maxlength' => true]) ?>
           
               
                    <?php echo $form->field($model, 'address')->textarea(['rows' => 4]) ?>
               

             
                    <?php echo $form->field($model, 'mobile_no')->textInput(['maxlength' => true]) ?>

                    <?php echo $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
              
             
                    <?php echo $form->field($model, 'current_isp')->textInput(['maxlength' => true]) ?>

                    <?php echo $form->field($model, 'current_contract_end_date')->widget(
                                            DatePicker::className(), [
                                                'clientOptions' => [
                                                    'autoclose' => true,
                                                    'format' => 'yyyy-mm-dd',
                                                    'startDate'=>date('Y-m-d')
                                                ]
                                        ]);?>
                     <?php echo $form->field($model, 'current_package')->textInput(['maxlength' => true]) ?>

                </div>
                <div class="col-md-6">

                   
                     <?php echo $form->field($model, 'current_isp_bill')->textInput() ?>
             
                    <?php 

                    $arrCurrency=Currency::find()->all();

                    $listCurrency=ArrayHelper::map($arrCurrency,'currency_id','currency');

                    echo $form->field($model, 'fk_currency_id')->dropDownList($listCurrency, ['prompt'=>'Select Currency']);
                    ?>
                    <?php 

                    $arrPackage=Package::find()->all();

                    $listPackage=ArrayHelper::map($arrPackage,'package_id','package_title');

                    echo $form->field($model, 'fk_package_id')->dropDownList($listPackage);
                    ?> 
                 
                   
                    <?php echo$form->field($model, 'package_speed')->textInput(['maxlength' => true]) ?>
              

                    <?php 

                    $arrSpeed=Speed::find()->all();

                    $listSpeed=ArrayHelper::map($arrSpeed,'speed_id','speed_type');

                    echo $form->field($model, 'fk_speed_id')->dropDownList($listSpeed);
                    ?> 

                    <?php echo$form->field($model, 'price_quote')->textInput() ?>


                    <?php echo $form->field($model, 'success_rate')->textInput() ?>
        

                    <?php echo $form->field($model, 'estimate_sign_up_date')->widget(
                                            DatePicker::className(), [
                                                'clientOptions' => [
                                                    'autoclose' => true,
                                                    'format' => 'yyyy-mm-dd',
                                                    'startDate'=>date('Y-m-d')
                                                ]
                                        ]);?>
                 
                    <?php echo $form->field($model, 'is_deal_closed')->radioList([ 'yes' => 'Yes', 'no' => 'No']); ?>

                </div> 
                <div class="btn-align">
                            <?= Html::submitButton($model->isNewRecord ? 'Add' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-primary' : 'btn btn-primary']) ?>

                            <?php if($model->isNewRecord){?>
                            <?= Html::submitButton('Save & Add More' , ['class' => 'btn btn-primary', 'name'=>'addmore' ,'value' => 'add']) ?>
                            <?php } ?>
                            
                            <?php echo Html::a('Cancel', ['index'], ['class' => 'btn btn-warning']) ;?>     
                </div>
            <?php ActiveForm::end(); ?>
	  </div>
     </div>
   </div>
   </div>


