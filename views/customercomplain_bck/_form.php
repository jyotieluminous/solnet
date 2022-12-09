<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use dosamigos\datepicker\DatePicker;
use dosamigos\datetimepicker\DateTimePicker;

use kartik\select2\Select2;

?>

	<div class="box box-default">
		<div class="box-body">
			<div class="tbllanguage-form">
				<?php 
				$form = ActiveForm::begin();
				?>
				<div class="row">
					<div class="col-md-12">
						<div class="form-group required col-md-12">
						<?php
							echo $form->field($model, 'fk_customer_id')->widget(Select2::classname(),[
	                                'model'=>$model,
	                                'data' => $data,
	                                'language' => 'en',
	                                'options' => ['placeholder' => 'Select Customer'],
	                                'pluginOptions' => [
	                                'allowClear' => true,
	                                'multiple'=>false
	                             ],
	                         ]);
                         ?>
						</div>						
					</div>
				</div>
				
				<div class="row">
					<div class="col-md-12">
						<div class="form-group col-md-6">
						<label class="control-label" for="email_address">Email address</label>
<input type="text" id="email_address" class="form-control" disabled="" <?php if(!$model->isNewRecord){ ?> value="<?php echo $cust_data['email'];?>" <?php }?>>
						</div>
						<div class="form-group col-md-6">
						<label class="control-label" for="mobile_no">Mobile Number</label>
<input type="text" id="mobile_no" class="form-control" disabled="" <?php if(!$model->isNewRecord){ ?> value="<?php echo $cust_data['mobile_no'];?>" <?php }?>>
						</div>						
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="form-group col-md-6">
						<label class="control-label" for="address">Installation Address</label>
<input type="text" id="address" class="form-control" disabled="" <?php if(!$model->isNewRecord){ ?> value="<?php echo $cust_data['billing_address'];?>" <?php }?>>
						</div>
						<div class="form-group col-md-6">
						<label class="control-label" for="phone_no">Phone Number</label>
<input type="text" id="phone_no" class="form-control" disabled="" <?php if(!$model->isNewRecord){ ?> value="<?php echo $cust_data['phone_number'];?>" <?php }?>>
						</div>						
					</div>
				</div>
				
				<div class="row">
					<div class="col-md-12">
						<div class="form-group col-md-6">
						<label class="control-label" for="package_title">Package Title</label>
<input type="text" id="package_title" class="form-control" disabled="" <?php if(!$model->isNewRecord){ ?> value="<?php echo $cust_data['package_title'];?>" <?php }?>>
						</div>
						<div class="form-group col-md-6">
						<label class="control-label" for="package_speed">Speed</label>
<input type="text" id="package_speed" class="form-control" disabled="" <?php if(!$model->isNewRecord){ ?> value="<?php echo $cust_data['speed'];?>" <?php }?>>
						</div>						
					</div>
				</div>
				
				
				<div class="row">
					<div class="col-md-12">
						<div class="form-group required col-md-6">
						<?php echo $form->field($model, 'ticket_number')->textInput(['readonly' => true]) ?>
						</div>
						<div class="col-md-6 form-group required">							
							<?php							
							echo  $form->field($model, 'complain_date')->widget(
								DateTimePicker::className(), [
									'clientOptions' => [
										'autoclose' => true,
										'size' => 'ms',
										'template' => '{input}',
										'format' => 'yyyy-mm-dd h:i',
										'inline' => true,
										'pickerPosition'=> "bottom-left",
										//'todayHighlight' => true
										//'startDate'=>date('Y-m-d')
									]
							]);
														
							// echo  $form->field($model, 'complain_date')->widget(
							// 	DateTimePicker::className(), [
							// 		'clientOptions' => [
							// 			'autoclose' => true,
							// 			'size' => 'ms',
							// 			'template' => '{input}',
							// 			'format' => 'yyyy-mm-dd h:i',
							// 			'inline' => true,
							// 			'pickerPosition'=> "bottom-left",
							// 			//'todayHighlight' => true
							// 			//'startDate'=>date('Y-m-d')
							// 		]
							// ]);
							?>
						</div>
					</div>
				</div>					
				
				<div class="row">
					<div class="col-md-12">
						<div class="form-group required col-md-6">
						<?php echo $form->field($model, 'caller_name')->textInput() ?>
						</div>
						<div class="col-md-6 form-group">
							<?php echo $form->field($model, 'alternative_email')->textInput(['maxlength' => true]) ?>							
						</div>						
					</div>
				</div>
				
				<div class="row">
					<div class="col-md-12">
						<div class="col-md-6 form-group required">
							<?php echo $form->field($model, 'phone_no_1')->textInput(['maxlength' => true]) ?>							
						</div>
						<div class="form-group col-md-6">
							<?php echo $form->field($model, 'phone_no_2')->textInput() ?>
						</div>						
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="form-group required col-md-6">						
						<?php echo $form->field($model, 'issue')->textArea(['maxlength' => true]) ?>
						</div>
						<div class="col-md-6 form-group required">							
							<?php echo $form->field($model, 'proposed_solution')->textArea(['maxlength' => true]) ?>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="form-group required col-md-6">						
						<?php echo $form->field($model, 'link_status')->dropDownList([''=>'Select Link Status','up' => 'UP', 'down' => 'Down', 'unstable' => 'UNSTABLE']); ?>
						</div>
						<div class="col-md-6 form-group required">							
							<?php echo $form->field($model, 'support_site')->dropDownList([''=>'Select Support Site','onsite' => 'Onsite', 'offsite' => 'Offsite']); ?>							 
							<input type="hidden" id="custom_data" name="custom_data" value="">
								
						</div>
					</div>
				</div>				
					
				<div class="form-group">
					<?php echo Html::submitButton('Submit', ['class'=> 'btn btn-primary']) ;?>	
					<?php echo Html::a('Cancel', Yii::$app->request->referrer, ['class' => 'btn btn-warning']) ?>
				</div>
				
				<?php ActiveForm::end(); ?>
			</div>
		</div>
	</div>

<script type="text/javascript">
$(document).ready(function() 
{
	// FILL THE CUSTOMER INFO ON CUSTOMER CHANGE DROPDOWN
	$('#tblcustomercomplains-fk_customer_id').change(function(){		
		var intCustId = $(this).val();
		var complainType = '<?php echo $complain_type;?>';
		//console.log(complain_type);
		//return false;
		if(intCustId!=''){			
			$.ajax({
			  url: '<?php echo yii::$app->request->baseUrl;  ?>/customercomplain/getcustomerdetails',
			  type: "GET",
			  data: {id : intCustId, complain_type:complainType},
			  dataType:'json',
			  cache: false,
			  success: function(response){
				$('#email_address').val(response.email);
				$('#mobile_no').val(response.mobile_no);
				$('#package_title').val(response.package_title);
				$('#package_speed').val(response.speed);
				$('#address').val(response.billing_address);
				$('#phone_no').val(response.phone_number);
				$('#tblcustomercomplains-ticket_number').val(response.ticket_no);
				//console.log(response.state_id + "||" + response.increment_value);
				$('#custom_data').val(response.state_id + "||" + response.increment_value);
				//console.log(response.email);
			  }
			});
			return false;
		}
	});
});
</script>