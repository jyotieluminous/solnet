<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use dosamigos\datepicker\DatePicker;

?>

	<div class="box box-default">
		<div class="box-body">
			<div class="tbllanguage-form">
				<?php 
				$form = ActiveForm::begin([
					'options' => [
								'enctype' => 'multipart/form-data'
								],
				]);
				?>
				<div class="row">
					<div class="col-md-12">
						<div class="form-group required col-md-6">
						
							<?php echo $form->field($model, 'user_type')->radioList(array('home'=>'Home','corporate'=>'Corporate'),['itemOptions' => ['class' =>'code-type-radio']]); ?>
						</div>
						<div class="col-md-6 form-group required">
							<?php echo $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="col-md-6 form-group">
							<?php echo $form->field($model, 'ktp_pass_no')->textInput(['maxlength' => true]) ?>
						</div>
						<div class="col-md-6 form-group required">
							<?php echo $form->field($model, 'billing_address')->textArea(['maxlength' => true]) ?>
						</div>
						
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="col-md-6 form-group required">
							<?php echo $form->field($model, 'fk_country_id')->dropDownList(
								$countryList,['onchange'=>'$.post( "'.Yii::$app->urlManager->createUrl('customer/state?id=').'"+$(this).val(), function( data ) { $( "select#customer-fk_state_id" ).html( data );
                });']); //['prompt'=>'Select Country'] ?>
						</div>
						<div class="col-md-6 form-group required">
							<?php echo $form->field($model, 'fk_state_id')->dropDownList(
								$stateList,['prompt'=>'Select State']); ?>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="col-md-6 form-group required">
							<?php echo $form->field($model, 'customer_type')->dropDownList(
								['Broadband'=>'Broadband','Dedicated'=>'Dedicated','Local Loop'=>'Local Loop'],['prompt'=>'Select Customer Type']); //['prompt'=>'Select Country'] ?>
						</div>
					</div>
				</div>

				<div class="row" >
					<div class="col-md-12">
						<div class="col-md-6 form-group required" id="home">
							<?php echo $form->field($model, 'email_address')->textInput(['maxlength' => true]) ?>
						</div>
						<?php
							$roleId = Yii::$app->user->identity->fk_role_id;
							if($roleId=='21')
							{
						?>
						<div class="col-md-6 form-group">
							<?php echo $form->field($model, 'agent_name')->textInput(['maxlength' => true]) ?>
						</div>
						<?php
							}
						?>
						
					</div>
				</div>
				<div class="row" id="corporate">
					<div class="col-md-12">
						<div class="col-md-6 form-group required">
							<?php echo $form->field($model, 'email_it')->textInput(['maxlength' => true]) ?>
						</div>
						<div class="col-md-6 form-group">
							<?php echo $form->field($model, 'email_finance')->textInput(['maxlength' => true]) ?>
						</div>
					</div>
					<div class="col-md-12">
						<div class="col-md-6 form-group ">
							<?php echo $form->field($model, 'it_pic')->textInput(['maxlength' => true]) ?>
						</div>
						
					</div>
				</div>
				
				<div class="row">
					<div class="col-md-12">
						<div class="col-md-6 form-group ">
							<?php echo $form->field($model, 'optional_email')->textInput(['maxlength' => true]) ?>
							
						</div>
						
					</div>
				</div>
				
				<div class="row">
					<div class="col-md-12">
						<div class="col-md-6 form-group required">
							<?php echo $form->field($model, 'mobile_no')->textInput(['maxlength' => true]) ?>
							<em>(Mobile No. should start with + sign)</em>
						</div>
						<div class="col-md-6 form-group">
							<?php echo $form->field($model, 'fixed_line_no')->textInput(['maxlength' => true]) ?>
							<em>(Fixed line No. should start with + sign)</em>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="col-md-6 form-group required">
							<?php echo $form->field($modelLinkCustPackage, 'installation_address')->textArea(['maxlength' => true]) ?>
							<?php echo $form->field($model, 'is_address_same')->checkbox() ?>
						</div>
						<div class="col-md-6 form-group required">
							<?php echo $form->field($modelLinkCustPackage, 'fk_package_id')->dropDownList(
								$packageList,['prompt'=>'Select Package']); ?>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="col-md-6 form-group required">
							<?php echo $form->field($modelLinkCustPackage, 'package_speed')->textInput(['maxlength' => true]) ?>
							
						</div>
						<div class="col-md-6 form-group required">
								<?php echo $form->field($modelLinkCustPackage, 'fk_speed_id')->dropDownList(
								$speedList); //,['prompt'=>'Select Speed'] ?>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="col-md-6 form-group required">
							<?php echo $form->field($modelLinkCustPackage, 'fk_currency_id')->dropDownList(
								$currencyList); //,['prompt'=>'Select Currency'] ?>
						</div>
						<div class="col-md-6 form-group required">
							<?php echo $form->field($modelLinkCustPackage, 'package_price')->textInput(['maxlength' => true]) ?>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="col-md-6 form-group required">
							<?php echo $form->field($modelLinkCustPackage, 'payment_type')->dropDownList(['advance' => 'PAY IN ADVANC', 'term' => 'TERM PAYMENT', 'bulk' => 'BULK PAYMENT']); //[''=>'Select Payment Type', ?>
						</div>
						<div class="col-md-6 form-group required term">
							<?php echo $form->field($modelLinkCustPackage, 'payment_term')->textInput(['maxlength' => true]) ?>
						</div>
						<div class="col-md-6 form-group required bulk">
							<?php echo $form->field($modelLinkCustPackage, 'bulk_price')->textInput(['maxlength' => true]) ?>
						</div>
					</div>
				</div>
				<div class="row picker">
					<div class="col-md-12">
						<div class="col-md-6 form-group required">
							<?php echo $form->field($modelLinkCustPackage, 'start_date')->widget(
								DatePicker::className(), [
									'clientOptions' => [
										'autoclose' => true,
										'format' => 'dd-mm-yyyy',
										//'startDate'=>date('d-m-Y')
									]
							]);?>
						</div>
						<div class="col-md-6 form-group required">
							<?php echo $form->field($modelLinkCustPackage, 'end_date')->widget(
								DatePicker::className(), [
									'clientOptions' => [
										'autoclose' => true,
										'format' => 'dd-mm-yyyy',
										//'startDate'=>date('d-m-Y')
									],
							]);?>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="col-md-6 form-group">
							<?php echo $form->field($modelLinkCustPackage, 'installation_fee')->textInput(['maxlength' => true]) ?>
						</div>
						<div class="col-md-6 form-group">
							<?php echo $form->field($modelLinkCustPackage, 'other_service_fee')->textInput(['maxlength' => true]) //,'readonly'=>true ?>
						</div>
						
					</div>
				</div>
				
				<div class="row">
					<div class="col-md-12">
						<div class="col-md-6 form-group">
							<?php echo $form->field($model, 'vat')->textInput(['maxlength' => true,'readonly'=>true]) ?>
						</div>
						<div class="col-md-6 form-group required">
							<?php echo $form->field($model, 'invoice_send_via')->radioList(array('email'=>'Email','hardcopy'=>'Hardcopy','both'=>'Both')); ?>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="col-md-6 form-group">
							<?php echo $form->field($modelLinkCustPackage, 'bundling_package')->textInput(['maxlength' => true]) ?>
						</div>
						
						<div class="col-md-6 form-group">
							<?php echo $form->field($model, 'po_wo_number')->textInput(['maxlength' => true]) ?>
						</div>
					</div>
				</div>	
				
				<div class="row">
					<div class="col-md-12">
						<div class="col-md-6 form-group">
							<?php echo $form->field($model, 'additional_info')->textInput(['maxlength' =>true]) ?>
							
						</div>
						
						
					</div>
					<div class="col-md-6 form-group">
							<?php echo $form->field($model, 'filepath')->fileInput(['maxlength' =>true ,'id'=>'uploadFile']) ?>
							<button id="btn-example-file-reset" type="button">Reset file</button>
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
		$('#corporate').hide();
		$('#customer-user_type input[type=radio]').change(function(){
    		if($(this).val()=='home')  {
				$('#corporate').hide();
				$('#home').show();
			}else{
				$('#corporate').show();
				$('#home').hide();
			}
      
      });
		
	  var pay_type = $('#linkcustomepackage-payment_type').val();	
		if(pay_type=='term'){
			  $('.term').show();
			  $('.picker').hide();
			  $('.bulk').hide();
		  }
		  else if(pay_type=='bulk')
		  {
			  $('.picker').show();
			  $('.term').hide();
			  $('.bulk').show();
		  }
		  else{
			  $('.picker').hide();
			  $('.term').hide();
			  $('.bulk').hide();
		  }
	  /*$('.term').hide();
	  $('.bulk').hide();
	  $('.picker').hide();*/
	  $('#linkcustomepackage-payment_type').change(function(){
		 
		  if($(this).val()=='term'){
			  $('.term').show();
			  $('.picker').hide();
			  $('.bulk').hide();
		  }
		  else if($(this).val()=='bulk')
		  {
			  $('.picker').show();
			  $('.term').hide();
			  $('.bulk').show();
		  }
		  else{
			  $('.picker').hide();
			  $('.term').hide();
			  $('.bulk').hide();
		  }
	  });
	  
	  $('#customer-is_address_same').click(function()
  	  {
		  if ($('#customer-is_address_same').is(":checked")){
			 var strBillingAddr =  $('#customer-billing_address').val();
			  $('#linkcustomepackage-installation_address').val(strBillingAddr);
		  }else{
			  $('#linkcustomepackage-installation_address').val('');
		  }
	  });
	  
		
		$('#customer-fk_state_id').change(function(){
		  var intStateId = $(this).val();
			
			if(intStateId!='')
				{
					$.ajax({
					  url: '<?php echo yii::$app->request->baseUrl;  ?>/customer/getvat',
					  type: "GET",
  					  data: {id : intStateId},
					  dataType:'json',
					  cache: false,
					  success: function(response){
						$("#customer-vat").val(response);
					  }
					});
					return false;
				}
	  });

		$('#btn-example-file-reset').on('click', function(e){
           var $el = $('#uploadFile');
           $el.wrap('<form>').closest('form').get(0).reset();
           $el.unwrap();
        });
   
	});
</script>