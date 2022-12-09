<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use dosamigos\datepicker\DatePicker;

$this->title = 'Add Existing Customer';
$form = ActiveForm::begin([
	'options' => [
					'enctype' => 'multipart/form-data'
				],
		]);
?>

	<div class="box box-default">
		<div class="box-body">
			<div class="tbllanguage-form View-Customer-sec ">
				<h2 align="center">Customer Details</h2>
				<div class="row">
					<div class="col-md-12">
						<div class="col-md-6 form-group required">
							<?php echo $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
						</div>
						<div class="col-md-6 form-group">
							<?php echo $form->field($model, 'ktp_pass_no')->textInput(['maxlength' => true]) ?>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="col-md-6 form-group required">
							<?php echo $form->field($model, 'billing_address')->textArea(['maxlength' => true]) ?>
						</div>
						<div class="col-md-6 form-group required">
							<?php echo $form->field($modelLinkCustPackage, 'installation_address')->textArea(['maxlength' => true]) ?>
							<?php echo $form->field($model, 'is_address_same')->checkbox() ?>
						</div>

					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="col-md-6 form-group required">
							<?php echo $form->field($model, 'fk_country_id')->dropDownList(
								$countryList); //['prompt'=>'Select Country'] ?>
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
				<div class="row" >
				<div class="col-md-12">
					<div class="form-group required col-md-6">
							<?php echo $form->field($model, 'user_type')->radioList(array('home'=>'Home','corporate'=>'Corporate'),['itemOptions' => ['class' =>'code-type-radio']]); ?>
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
						<div class="col-md-6 form-group ">
							<?php echo $form->field($model, 'po_wo_number')->textInput(['maxlength' => true]) ?>
						</div>
						<div class="col-md-6 form-group ">
							<?php echo $form->field($model, 'additional_info')->textInput(['maxlength' => true]) ?>
						</div>
					</div>
				</div>
					<h2 align="center">Package Details</h2>
				<div class="row">
					<div class="col-md-12">

						<div class="col-md-6 form-group required">
							<?php echo $form->field($modelLinkCustPackage, 'fk_package_id')->dropDownList(
								$packageList,['prompt'=>'Select Package']); ?>
						</div>

					<div class="col-md-6 form-group">
							<?php echo $form->field($modelLinkCustPackage, 'bundling_package')->textInput(['maxlength' => true]) ?>
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
					</div>
				</div>
				<div class="row picker">
					<div class="col-md-12">
						<div class="col-md-6 form-group required">
							<?php echo $form->field($modelLinkCustPackage, 'start_date')->widget(
								DatePicker::className(), [
									'clientOptions' => [
										'autoclose' => true,
										'format' => 'yyyy-mm-dd',
										'startDate'=>date('Y-m-d')
									]
							]);?>
						</div>
						<div class="col-md-6 form-group required">
							<?php echo $form->field($modelLinkCustPackage, 'end_date')->widget(
								DatePicker::className(), [
									'clientOptions' => [
										'autoclose' => true,
										'format' => 'yyyy-mm-dd',
										'startDate'=>date('Y-m-d')
									],
							]);?>
						</div>
					</div>
					<div class="col-md-12">
						<div class="col-md-6 form-group required">
							<?php echo $form->field($modelLinkCustPackage, 'bulk_price')->textInput(['maxlength' => true]); ?>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="col-md-6 form-group">
						<?php echo $form->field($modelLinkCustPackage, 'other_service_fee')->textInput(['maxlength' => true]) //,'readonly'=>true ?>
						</div>
						<div class="col-md-6 form-group">
							<?php echo $form->field($modelLinkCustPackage, 'contract_number')->textInput(['maxlength' => true]) ?>
						</div>

					</div>
				</div>


				<div class="row">
					<div class="col-md-12">
						<div class="col-md-6 form-group ">
							<?php echo $form->field($model, 'vat')->textInput(['maxlength' => true,'readonly'=>true]) ?>
						</div>
						<div class="col-md-6 form-group required">
							<?= $form->field($modelLinkCustPackage, 'invoice_start_date')->widget(
								DatePicker::className(), [
									'clientOptions' => [
										'autoclose' => true,
										'format' => 'dd-mm-yyyy',

									]
							]);?>
						</div>

					</div>
				</div>

				<div class="row">
					<div class="col-md-12">
						<div class="col-md-6 form-group required">

							<?= $form->field($modelLinkCustPackage, 'contract_start_date')->widget(
								DatePicker::className(), [
									'clientOptions' => [
										'autoclose' => true,
										'format' => 'dd-mm-yyyy',

									]
							]);?>
						</div>
						<div class="col-md-6 form-group required">
							<?= $form->field($modelLinkCustPackage, 'contract_end_date')->widget(
								DatePicker::className(), [
									'clientOptions' => [
										'autoclose' => true,
										'format' => 'dd-mm-yyyy',

									],
							]);?>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-12">

						<div class="col-md-6 form-group required">
							<?php echo $form->field($model, 'invoice_send_via')->radioList(array('email'=>'Email','hardcopy'=>'Hardcopy','both'=>'Both')); ?>
						</div>
					</div>
				</div>

				<h2 align="center">Bank Details</h2>

              <div class="row">
					<div class="col-md-12 form-group required text-center">
						 <?php
						if(!empty($modelLinkCustPackage->is_solnet_bank))
						{
							if($modelLinkCustPackage->is_solnet_bank=='yes')
							{
								$modelLinkCustPackage->bank_type = 'solnet';
							}elseif($modelLinkCustPackage->is_solnet_bank=='no')
							{
								$modelLinkCustPackage->bank_type = 'virtual';
							}
						}
						?>
							 <?php echo $form->field($modelLinkCustPackage, 'is_solnet_bank')->radioList(array('virtual'=>'Bank Virtual ACC','solnet'=>'SOLNET Bank Account'),['itemOptions' => ['class' =>'bank-type-radio']]); ?>
					</div>
			  </div>
              <div class="row virtual">
              	<div class="col-md-12 form-group required text-center">

              		<div class="col-md-6 text-center">
              			<?php echo $form->field($modelLinkCustPackage, 'bank_name')->textInput(); ?>
					</div>
					<div class="col-md-6 text-center">
              			<?php echo $form->field($modelLinkCustPackage, 'virtual_acc_no')->textInput(); ?>
					</div>
              	</div>
              </div>

			  <div class="row virtual">
              	<div class="col-md-12 form-group required text-center">
              		<div class="col-md-6 text-center">
              			<?php echo $form->field($modelLinkCustPackage, 'account_name')->textInput(); ?>
              		</div>
              	</div>
              </div>


              <div class="row solnet">
              	<div class="col-md-12 form-group required text-center">
              		<!--<div class="col-md-3"></div> -->
              		<div class="col-md-6 text-center">
              			<?php echo $form->field($modelLinkCustPackage, 'bank_id')->dropDownList(
								$bankList,['prompt'=>'Select Bank', 'onchange'=>'$.post("'.Yii::$app->urlManager->createUrl('customer/getbankdetails?id=').'"+$(this).val(),function( data ) {
								var processData = JSON.parse(data);
								$("#linkcustomepackage-bankname").val(processData[0]);
								$("#linkcustomepackage-accname").val(processData[1]);
								$("#linkcustomepackage-bankcurrency").val(processData[3]);
								$("#linkcustomepackage-bankbranch").val(processData[2]);

								});'
							]); ?>
              		</div>
              		<div class="col-md-3"></div>
              	</div>
              </div>
			  <div class="row solnet">
				<div class="col-md-12 form-group">
					<div class="col-md-6 text-center">
						<?php echo $form->field($modelLinkCustPackage, 'bankname')->textInput(['readOnly'=>true]); ?>
					</div>
					<div class="col-md-6 text-center">
						<?php echo $form->field($modelLinkCustPackage, 'accname')->textInput(['readOnly'=>true]); ?>
					</div>
				</div>

			  </div>
			  <div class="row solnet">
				<div class="col-md-12 form-group">
					<div class="col-md-6 text-center">
						<?php echo $form->field($modelLinkCustPackage, 'bankcurrency')->textInput(['readOnly'=>true]); ?>
					</div>
					<div class="col-md-6 text-center">
						<?php echo $form->field($modelLinkCustPackage, 'bankbranch')->textInput(['readOnly'=>true]); ?>
					</div>
				</div>

			  </div>

				<div class="row">
					<div class="col-md-12">
						<div class="col-md-6 form-group">
							<?php echo $form->field($model, 'filepath')->fileInput(['maxlength' => true,'id' => 'uploadFile']) ?>
								<button id="btn-example-file-reset" type="button">Reset file</button>
						</div>
					
					</div>
				</div>
				<div class="form-group">
					<?php echo Html::submitButton('Submit', ['class'=> 'btn btn-primary']) ;?>
					<?php echo Html::a('Cancel', Yii::$app->request->referrer, ['class' => 'btn btn-warning']) ?>
				</div>


			</div>
		</div>
	</div>
<?php ActiveForm::end(); ?>
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

	  $('.term').hide();
	  $('.picker').hide();
	  $('#linkcustomepackage-payment_type').change(function(){

		  if($(this).val()=='term'){
			  $('.term').show();
			  $('.picker').hide();
		  }
		  else if($(this).val()=='bulk')
		  {
			  $('.picker').show();
			  $('.term').hide();
		  }
		  else{
			  $('.picker').hide();
			  $('.term').hide();
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

	var is_solnet_bank = '<?php echo $modelLinkCustPackage->is_solnet_bank; ?>';
		if(is_solnet_bank=='yes'){
			  $('.solnet').show();
			  $('.virtual').hide();
		}else if(is_solnet_bank=='no'){
			  $('.virtual').show();
			  $('.solnet').hide();
		}else{
			  $('.solnet').hide();
	  		  $('.virtual').hide();
		}
		//$('.solnet').hide();
		//$('.virtual').hide();
		$('#linkcustomepackage-is_solnet_bank input[type=radio]').change(function(){

    		if($(this).val()=='virtual')  {
				$('.solnet').hide();
				$('.virtual').show();
			}else{
				$('.solnet').show();
				$('.virtual').hide();
			}

      });

        $('#btn-example-file-reset').on('click', function(e){
           var $el = $('#uploadFile');
           $el.wrap('<form>').closest('form').get(0).reset();
           $el.unwrap();
        });

	});
</script>
