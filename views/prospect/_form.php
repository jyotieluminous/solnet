<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use dosamigos\datepicker\DatePicker;
use app\models\Currency;
use app\models\Speed;
use app\models\Package;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
/* @var $this yii\web\View */
/* @var $model app\models\Prospect */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="box box-primary">
    <div class="box-body">
        <div class="row">
            <div class="prospect-form View-Customer-sec">

                <?php $form = ActiveForm::begin(['id'=>'prospect-form']); ?>
                <div class="col-md-12" >
                  <div class="box box-default">
                    <div class="box-body">
                        <h3 align="center">Personal Details</h3>
                        <div class="col-md-6">

                        <?php echo $form->field($model, 'customer_name')->textInput(['maxlength' => true]) ?>
                         <?php echo $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
                        <?php echo $form->field($model, 'person_incharge')->textInput(['maxlength' => true]) ?>
                        <?php 
                        echo $form->field($model, 'fk_state_id')->dropDownList($statesList,['prompt'=>'Select State']); ?>
                        </div>
                        <div class="col-md-6">

                             <?php echo $form->field($model, 'mobile_no')->textInput(['maxlength' => true]) ?>

                        <?php echo $form->field($model, 'address')->textarea(['rows' => 4]) ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12" >
                  <div class="box box-default">
                    <div class="box-body">
                    <h3 align="center"><u>Current ISP Details</u></h3>
                        <div class="col-md-6">

                        <?php echo $form->field($model, 'current_isp')->textInput(['maxlength' => true]) ?>
                        <?php echo $form->field($model, 'current_package')->textInput(['maxlength' => true]) ?>
                        </div>
                        <div class="col-md-6">
                        <?php echo $form->field($model, 'current_contract_end_date')->widget(
                                            DatePicker::className(), [
                                                'clientOptions' => [
                                                    'autoclose' => true,
                                                    'format' => 'dd-mm-yyyy',
                                                ]
                                        ]);?>
                        <div class="col-md-5">
                         <?php echo $form->field($model, 'current_isp_bill')->textInput() ?>
                         </div>
                         <div class="col-md-5">
                             <?php

                            $arrCurrency=Currency::find()->where(['status'=>'active'])->all();

                            $listCurrency=ArrayHelper::map($arrCurrency,'currency_id','currency');

                            echo $form->field($model, 'current_currency')->dropDownList($listCurrency);
                            ?>
                        </div>

                        </div>

                    </div>
                </div>
            </div>
             <div class="col-md-12" >
                  <div class="box box-default">
                    <div class="box-body">
                    <h3 align="center"><u>Proposed ISP Details</u></h3>
                        <div class="col-md-6">

                        <?php

                        $arrPackage=Package::find()->where(['is_deleted'=>'0'])->all();

                        $listPackage=ArrayHelper::map($arrPackage,'package_id','package_title');

                        echo $form->field($model, 'fk_package_id')->dropDownList($listPackage ,['prompt'=>'Select Package']);
                        ?>
                        <div class="col-md-5 ">
                            <?php echo$form->field($model, 'package_speed')->textInput(['maxlength' => true]) ?>
                        </div>
                        <div class="col-md-5 ">
                            <?php

                            $arrSpeed=Speed::find()->all();

                            $listSpeed=ArrayHelper::map($arrSpeed,'speed_id','speed_type');

                            echo $form->field($model, 'fk_speed_id')->dropDownList($listSpeed);
                            ?>
                        </div>
                        <div class="col-md-5">
                              <?php echo$form->field($model, 'price_quote')->textInput() ?>
                        </div>
                        <div class="col-md-5">
                             <?php

                            $arrCurrency=Currency::find()->all();

                            $listCurrency=ArrayHelper::map($arrCurrency,'currency_id','currency');

                            echo $form->field($model, 'fk_currency_id')->dropDownList($listCurrency);

                            ?>

                        </div>
                        <div class="col-md-5">
                        <?php echo $form->field($model, 'is_deal_closed')->radioList([ 'yes' => 'Yes', 'no' => 'No']); ?>
                        </div>
                        </div>
                        <div class="col-md-6">

                        <?php echo $form->field($model, 'success_rate')->dropDownList(['50' => '50', '70' =>'70','90'=>'90'], ['prompt' => 'Select Success Rate']) ?>

                        <?php echo $form->field($model, 'estimate_sign_up_date')->widget(
                                    DatePicker::className(), [
                                        'clientOptions' => [
                                            'autoclose' => true,
                                            'format' => 'dd-mm-yyyy',
                                            'startDate'=>date('d-m-Y')
                                        ]
                                    ]);?>
                        <?php echo $form->field($model, 'quotation_date')->widget(
                                    DatePicker::className(), [
                                        'clientOptions' => [
                                            'autoclose' => true,
                                            'format' => 'dd-mm-yyyy',
                                            'startDate'=>date('d-m-Y')
                                        ]
                                    ]);?>



                        </div>

                    </div>
                </div>
            </div>

            <div class="btn-align">
                            <?= Html::submitButton($model->isNewRecord ? 'Add' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-primary ' : 'btn btn-primary ','onSubmit'=>"document.add.disabled = true"]) ?>

                            <?php if($model->isNewRecord){?>
                            <?= Html::submitButton('Save & Add More' , ['class' => 'btn btn-primary submit', 'name'=>'addmore' ,'value' => 'add']) ?>
                            <?php } ?>

                            <?php echo Html::a('Cancel', ['index'], ['class' => 'btn btn-warning']) ;?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
        </div>
     </div>
   </div>

<?php
$js2 = <<<JS
$(document).on("beforeValidate", "form", function(event, messages, deferreds) {
    $(this).find(':submit').attr('disabled', true);
    console.log('BEFORE VALIDATE TEST');
}).on("afterValidate", "form", function(event, messages, errorAttributes) {
    console.log('AFTER VALIDATE TEST');
    if (errorAttributes.length > 0) {
        $(this).find(':submit').attr('disabled', false);
    }
});
JS;

$this->registerJs($js2);
?>
