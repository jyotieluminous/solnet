<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use dosamigos\datepicker\DatePicker;
$this->title = 'Update Customer';
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
						<div class="col-md-6 form-group required">
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
							<?php echo $form->field($model, 'customer_type')->dropDownList(
								['Broadband'=>'Broadband','Dedicated'=>'Dedicated','Local Loop'=>'Local Loop'],['prompt'=>'Select Customer Type']); //['prompt'=>'Select Country'] ?>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="col-md-6 form-group required">
							<?php echo $form->field($model, 'fk_country_id')->dropDownList(
								$countryList,['prompt'=>'Select Country','onchange'=>'$.post( "'.Yii::$app->urlManager->createUrl('customer/state?id=').'"+$(this).val(), function( data ) { $( "select#customer-fk_state_id" ).html( data );
                });']); ?>
						</div>
						<div class="col-md-6 form-group required">
							<?php echo $form->field($model, 'fk_state_id')->dropDownList(
								$stateList,['prompt'=>'Select State']); ?>
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
						<div class="col-md-6 form-group ">
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
						<div class="col-md-6 form-group required">
							<?php echo $form->field($model, 'fixed_line_no')->textInput(['maxlength' => true]) ?>
							<em>(Fixed line No. should start with + sign)</em>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="col-md-6 form-group required">
							<?php echo $form->field($modelLinkCustPackage, 'installation_address')->textArea(['maxlength' => true]) ?>
							<?php if($model->is_address_same=='yes') { $model->is_address_same =1; } ?>
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
								$speedList,['prompt'=>'Select Speed']); ?>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="col-md-6 form-group required">
							<?php echo $form->field($modelLinkCustPackage, 'fk_currency_id')->dropDownList(
								$currencyList,['prompt'=>'Select Currency']); ?>
						</div>
						<div class="col-md-6 form-group required">
							<?php echo $form->field($modelLinkCustPackage, 'package_price')->textInput(['maxlength' => true]) ?>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="col-md-6 form-group required">
							<?php echo $form->field($modelLinkCustPackage, 'payment_type')->dropDownList([''=>'Select Payment Type','advance' => 'PAY IN ADVANC', 'term' => 'TERM PAYMENT', 'bulk' => 'BULK PAYMENT']); ?>
						</div>
						<div class="col-md-6 form-group required term">
							<?php echo $form->field($modelLinkCustPackage, 'payment_term')->textInput(['maxlength' => true]) ?>
						</div>
						<div class="col-md-6 form-group required pay_in_advanc_grace_period">
							<?php echo $form->field($modelLinkCustPackage, 'pay_in_advanc_grace_period')->textInput(['maxlength' => true])->label('PAY IN ADVANC Grace period (in days):') ?>
						</div>
						<div class="col-md-6 form-group required bulk_grace_period">
							<?php echo $form->field($modelLinkCustPackage, 'bulk_grace_period')->textInput(['maxlength' => true])->label('BULK PAYMENT Grace period (in days ):') ?>
						</div>
						<div class="col-md-6 form-group required term_grace_period">
							<?php echo $form->field($modelLinkCustPackage, 'term_grace_period')->textInput(['maxlength' => true])->label('TERM PAYMENT Grace period (in days ):') ?>
						</div>
						
					</div>
				</div>
				<div class="row picker">
					<div class="col-md-12">
						<div class="col-md-6 form-group required">
						<?php
							$strStartDate = strtotime($modelLinkCustPackage->bulk_pay_start);
							if($modelLinkCustPackage->bulk_pay_start!='0000-00-00 00:00:00')
							{
								$modelLinkCustPackage->start_date = date('Y-m-d',$strStartDate);
							}else{
								$modelLinkCustPackage->start_date = '';
							}
							
							$strEndDate = strtotime($modelLinkCustPackage->bulk_pay_end);
							if($modelLinkCustPackage->bulk_pay_end!='0000-00-00 00:00:00')
							{
								$modelLinkCustPackage->end_date = date('Y-m-d',$strEndDate);
							}else{
								$modelLinkCustPackage->end_date = '';
							}
						?>
							<?= $form->field($modelLinkCustPackage, 'start_date')->widget(
								DatePicker::className(), [
									'clientOptions' => [
										'autoclose' => true,
										'format' => 'yyyy-mm-dd',
										'startDate'=>date('Y-m-d')
									]
							]);?>
						</div>
						<div class="col-md-6 form-group required">
							<?= $form->field($modelLinkCustPackage, 'end_date')->widget(
								DatePicker::className(), [
									'clientOptions' => [
										'autoclose' => true,
										'format' => 'yyyy-mm-dd',
										'startDate'=>date('Y-m-d')
									]
							]);?>
						</div>
					</div>
					<div class="col-md-12">
						<div class="col-md-6 form-group required ">
							<?php echo $form->field($modelLinkCustPackage, 'bulk_price')->textInput(['maxlength' => true]) ?>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="col-md-6 form-group required">
							<?php echo $form->field($modelLinkCustPackage, 'installation_fee')->textInput(['maxlength' => true]) ?>
						</div>
						<div class="col-md-6 form-group">
							<?php echo $form->field($modelLinkCustPackage, 'other_service_fee')->textInput(['maxlength' => true]) //,'readonly'=>true ?>
						</div>
						
					</div>
				</div>
				
				<div class="row">
					<div class="col-md-12">
						<div class="col-md-6 form-group required">
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
							<?php echo $form->field($modelLinkCustPackage, 'bundling_package')->textArea(['maxlength' => false,'id' =>'editor1']) ?>
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
						<div class="col-md-6 form-group">
							<?php echo $form->field($model, 'enable_disconnection')->checkbox(['maxlength' =>true]) ?>
							
						</div>
						
						</div>
						<div class="col-md-12">
						<div class="col-md-6 form-group">
							<?php echo $form->field($model, 'microtik_ip')->textInput(['maxlength' =>true]) ?>
							
						</div>
					</div>
						<div class="col-md-6 form-group required one-line-bx">
							<?php echo $form->field($model, 'filepath')->fileInput(['maxlength' => true,'id'=>'uploadFile']) ?>
							<?php
									if(!empty($model->filepath))
									{
										echo "<style>input[type='file'] { color: transparent;}</style>";
										echo Html::a("<img src=".Yii::getAlias('@web')."/web/images/pdf.png width=50px height=50px >", ['download', 'strFileName' => $model->filepath], ['style' => 'margin: -70px 0px 0px 180px;']) ;
										echo $model->filepath;
									}
							?>
							<button id="btn-example-file-reset" type="button">Reset file</button>
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
		
		CKEDITOR.replace('editor1');
		 for (var i in CKEDITOR.instances) {
                
                CKEDITOR.instances[i].on('change', function() { CKEDITOR.instances[i].updateElement() });
                
        }  
		var strUserType = $(".code-type-radio:checked").val();
		if(strUserType=='home')  {
				$('#corporate').hide();
				$('#home').show();
			}else if(strUserType=='corporate'){
				$('#corporate').show();
				$('#home').hide();
			}else{
				$('#corporate').hide();
				$('#home').hide();
			}
		 
		//$('#corporate').hide();
		$('#customer-user_type input[type=radio]').change(function(){
    		if($(this).val()=='home')  {
				$('#corporate').hide();
				$('#home').show();
			}else{
				$('#corporate').show();
				$('#home').hide();
			}
      
      });
		
	  var intPaymentType = '<?php echo $model->linkcustomerpackage->payment_type; ?>';
		if(intPaymentType=='term'){
			  $('.term').show();
			  $('.bulk').hide();
			  $('.picker').hide();
			  $('.pay_in_advanc_grace_period').hide();
			  $('.bulk_grace_period').hide();
			  $('.term_grace_period').show();
			 
		}else if(intPaymentType=='bulk'){
			  $('.picker').show();
			  $('.bulk').show();
			  $('.term').hide();
			  $('.pay_in_advanc_grace_period').hide();
			  $('.bulk_grace_period').show();
			  $('.term_grace_period').hide();
			  grace_period_type = 'BULK_PAYMENT';
			  grace_period_id= 'linkcustomepackage-bulk_grace_period';
		}else{
			  $('.term').hide();
			  $('.bulk').hide();
	  		  $('.picker').hide();
	  		  $('.pay_in_advanc_grace_period').show();
			  $('.bulk_grace_period').hide();
			  $('.term_grace_period').hide();	
			  grace_period_type = 'PAY_IN_ADVANC';
			  grace_period_id= 'linkcustomepackage-pay_in_advanc_grace_period';
		}
		
	  
	  $('#linkcustomepackage-payment_type').change(function(){
		 
		  var grace_period_type= '';
		  var grace_period_id= '';

		  if($(this).val()=='term'){
		  	  $('.pay_in_advanc_grace_period').hide();
		  	  $('.bulk_grace_period').hide();
		  	  $('.term_grace_period').show();
			  $('.term').show();
			  $('.picker').hide();
			  grace_period_type = 'TERM_PAYMENT';
			  grace_period_id= 'linkcustomepackage-term_grace_period';
		  }
		  else if($(this).val()=='bulk')
		  {
			  $('.picker').show();
			  $('.term').hide();
			  $('.pay_in_advanc_grace_period').hide();
			  $('.bulk_grace_period').show();
			  $('.term_grace_period').hide();
			  grace_period_type = 'BULK_PAYMENT';
			  grace_period_id= 'linkcustomepackage-bulk_grace_period';
		  }
		  else{
			  $('.picker').hide();
			  $('.term').hide();
			  $('.pay_in_advanc_grace_period').show();
			  $('.bulk_grace_period').hide();
			  $('.term_grace_period').hide();	
			  grace_period_type = 'PAY_IN_ADVANC';
			  grace_period_id= 'linkcustomepackage-pay_in_advanc_grace_period';
		  }
		   $.ajax({
			  url: '<?php echo yii::$app->request->baseUrl;?>/customer/getgraceperiodtype',
			  type: "GET",
				  data: {id : grace_period_type},
			  dataType:'json',
			  cache: false,
			  success: function(response){
				$("#"+grace_period_id).val(response);
			  }
			});
	  });
	  
	  $('#customer-is_address_same').click(function()
  	  {
		  if ($('#customer-is_address_same').is(":checked")){
			 var strBillingAddr =  $('#customer-billing_address').val();
			  $('#linkcustomepackage-installation_address').val(strBillingAddr);
		  }else{
			  //$('#linkcustomepackage-installation_address').val('<?php //echo $modelLinkCustPackage->installation_address; ?>');
			  $('#linkcustomepackage-installation_address').val('');
		  }
	  });
	  
		
		$("#customer-vat").val('<?php echo $model->state->vat; ?>');
		
		
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