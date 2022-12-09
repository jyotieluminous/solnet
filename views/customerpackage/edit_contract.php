<?php
/*
############################################################################################
# eLuminous Technologies - Copyright (@) http://eluminoustechnologies.com
# This code is written by eLuminous Technologies, Its a sole property of
# eLuminous Technologies and cant be used / modified without license.
# Any changes/ alterations, illegal uses, unlawful distribution, copying is strictly
# prohibhited
############################################################################################
# Name : edit_contract.php
# Created on : 28th June 2017 by Swati Jadhav.
# Update on  : 28th June 2017 by Swati Jadhav.
# Purpose : Edit contract number and contract status of diconnected customers
############################################################################################
*/
use yii\helpers\Html;
use dosamigos\datepicker\DatePicker;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
?>
<div class="box box-default">
	<div class="box-body">
		<div class="tbllanguage-form">
			<?php $form = ActiveForm::begin(['id'=>'edit_contract_form',
				'action' =>['customerpackage/submitcontract', 'id' =>$id]
			])?>
			 <div class="prospect-form">
                 <div class="col-md-12" >
                   
                        <div class="col-md-6 form-group">
                        	<label>Customer ID :</label>
                            <?php echo $model->customer->solnet_customer_id; ?><br>
                            
                            <label>Customer Name :</label>
                             <?php echo ucfirst($model->customer->name); ?><br><br>
                        

                            <label> Contract Number :</label>
                            <?php echo $form->field($model, 'contract_number')->textInput(['maxlength' => true])->label(false) ?>

                            <label> Contract Status:</label>
                            
                             <?php echo $form->field($model, 'contract_status')->dropDownList(['sent' => 'Sent', 'returned' =>'Returned','no_contract'=>'No Contract'], ['prompt' => 'Select Contract Status'])->label(false) ?>


                           
                       </div>
                       <div class="col-md-6 form-group">
                            
                            <label>Package Title :</label>
                            <?php echo $model->package->package_title;?>
                            

                            <label>Package Speed :</label>
                            <?php echo $model->package_speed; echo ' '; echo $model->speed->speed_type;?><br></br>

                            <?php
                            $strContractStartDate = strtotime($model->contract_start_date);
                          if($model->contract_start_date=='0000-00-00')
                          {
                            $model->contract_start_date = '';
                          }else{
                            $model->contract_start_date = date('Y-m-d',$strContractStartDate);
                          }

                          $strContractEndDate = strtotime($model->contract_end_date);
                          if($model->contract_end_date=='0000-00-00')
                          {
                            $model->contract_end_date = '';
                          }else{
                            $model->contract_end_date = date('Y-m-d',$strContractEndDate);
                          }
                                  
                             ?>

                            <?php echo $form->field($model, 'contract_start_date')->widget(
                              DatePicker::className(), [
                                'clientOptions' => [
                                  'autoclose' => true,
                                  'format' => 'yyyy-mm-dd',
                                  //'startDate'=>date('Y-m-d')
                                ]
                            ]);?>


                            <?php echo $form->field($model, 'contract_end_date')->widget(
                              DatePicker::className(), [
                                'clientOptions' => [
                                  'autoclose' => true,
                                  'format' => 'yyyy-mm-dd',
                                  //'startDate'=>date('Y-m-d')
                                ]
                            ]);?>
                       </div>
                
              </div>
           
			<div class="form-group" align="center">
			<?php echo Html::submitButton('Submit', ['class'=> 'btn btn-primary']) ;?>	
			<?php echo Html::button('Quit', ['class'=> 'btn btn-default closemodal']) ;?>
			</div>
		</div>
			<?php ActiveForm::end(); ?>
	 </div>
   </div>
</div>

<script type="text/javascript">
	$('.closemodal').click(function() {
    $('#modal').modal('hide');
});
</script>