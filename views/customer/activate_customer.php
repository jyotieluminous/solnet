<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use dosamigos\datepicker\DatePicker;
$this->title = 'Activate Customer';
$this->title = 'Activate Customer';
$this->params['breadcrumbs'][] = ['label' => 'Manage pending activations', 'url' => ['customer/pending']];
$this->params['breadcrumbs'][] = $this->title;
?>

	<div class="box box-default">
		<div class="box-body">
			<div class="tbllanguage-form View-Customer-sec">
				<h2 align="left">Package Details</h2>
				<div class="row">
					<div class="col-md-12">
						<div class="col-md-6 form-group required">
							<label>Name :</label>
							<?php echo ucfirst($model->name); ?>
						</div>
						<?php if($model->user_type=='home') { ?>
						<div class="col-md-6 form-group required">
							<label>Email :</label>
							<?php echo $model->email_address; ?>
						</div>
						<?php }elseif($model->user_type=='corporate'){ ?>
						<div class="col-md-6 form-group required">
							<label>Email :</label>
							<?php echo $model->email_it; ?>
						</div>
						<?php }?>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="col-md-6 form-group required">
							<label>Mobile No. :</label>
							<?php echo $model->mobile_no; ?>
						</div>
						<div class="col-md-6 form-group required">
							<label>Package Title :</label>
							<?php echo $modelLinkCustPackage->package->package_title; ?>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="col-md-6 form-group required">
							<label>Package Speed :</label>
							<?php echo $modelLinkCustPackage->package_speed.' '.$modelLinkCustPackage->speed->speed_type; ?>
						</div>
						<div class="col-md-6 form-group required">
								<label>Package Price :</label>
								<?php echo $modelLinkCustPackage->currency->currency.' '. number_format($modelLinkCustPackage->package_price,2); ?>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="col-md-6 form-group required">
							<label>Payment Type :</label>
							<?php echo ucfirst($modelLinkCustPackage->payment_type); ?>
						</div>
						<div class="col-md-6 form-group required">
						<?php if($modelLinkCustPackage->payment_type=='term') { ?>
									<label>Term :</label>
									<?php echo $modelLinkCustPackage->payment_term.' (days)'; ?>
						<?php }elseif($modelLinkCustPackage->payment_type=='bulk') { ?>

									<?php
										$strStartDate = strtotime($modelLinkCustPackage->bulk_pay_start);
										if($modelLinkCustPackage->bulk_pay_start!='0000-00-00 00:00:00')
										{
											$modelLinkCustPackage->start_date = date('d-m-Y',$strStartDate);
										}else{
											$modelLinkCustPackage->start_date = '';
										}

										$strEndDate = strtotime($modelLinkCustPackage->bulk_pay_end);
										if($modelLinkCustPackage->bulk_pay_end!='0000-00-00 00:00:00')
										{
											$modelLinkCustPackage->end_date = date('d-m-Y',$strEndDate);
										}else{
											$modelLinkCustPackage->end_date = '';
										}
									?>
									<label>Bulk Date :</label>
									<?php echo $modelLinkCustPackage->start_date.' <b>TO</b> '.$modelLinkCustPackage->end_date; ?>

						<?php } ?>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="col-md-6 form-group required">
							<label>Invoice Send Via :</label>
							<?php echo ucfirst($model->invoice_send_via); ?>
						</div>
					</div>
				</div>
				<?php $form = ActiveForm::begin(); ?>
				<div class="row align-center">
					<div class="col-md-12">
						<!--<div class="col-md-4"></div> -->
						<div class="col-md-4 form-group required">
						<?php
										$strInvoiceStartDate = strtotime($modelLinkCustPackage->invoice_start_date);
										if($modelLinkCustPackage->invoice_start_date=='0000-00-00 00:00:00' || is_null($modelLinkCustPackage->invoice_start_date))
										{
											$modelLinkCustPackage->invoice_start_date = date('d-m-Y');
										}else{
											$modelLinkCustPackage->invoice_start_date = date('d-m-Y',$strInvoiceStartDate);
										}
							?>
						<?php echo $form->field($modelLinkCustPackage, 'invoice_start_date')->widget(
								DatePicker::className(), [
								
									'clientOptions' => [
										'autoclose' => true,
										'format' => 'dd-mm-yyyy',
										'date' => date('d-m-Y'),
									]
							]);?>
						</div>
						<div class="col-md-4"></div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="col-md-6 form-group required">
							<?php
										$strContractStartDate = strtotime($modelLinkCustPackage->contract_start_date);
										if($modelLinkCustPackage->contract_start_date=='0000-00-00' || is_null($modelLinkCustPackage->contract_start_date))
										{
											$modelLinkCustPackage->contract_start_date = date('d-m-Y');
										}else{
											$modelLinkCustPackage->contract_start_date = date('d-m-Y',$strContractStartDate);
										}

										$strContractEndDate = strtotime($modelLinkCustPackage->contract_end_date);
										if($modelLinkCustPackage->contract_end_date=='0000-00-00' || is_null($modelLinkCustPackage->contract_end_date))
										{
											$modelLinkCustPackage->contract_end_date = date('d-m-Y');
										}else{
											$modelLinkCustPackage->contract_end_date = date('d-m-Y',$strContractEndDate);
										}
							?>
							<?php echo $form->field($modelLinkCustPackage, 'contract_start_date')->widget(
								DatePicker::className(), [
									'clientOptions' => [
										'autoclose' => true,
										'format' => 'dd-mm-yyyy',

									]
							]);?>
						</div>
						<div class="col-md-6 form-group required">
							<?php echo $form->field($modelLinkCustPackage, 'contract_end_date')->widget(
								DatePicker::className(), [
									'clientOptions' => [
										'autoclose' => true,
										'format' => 'dd-mm-yyyy',

									]
							]);?>
						</div>
					</div>
				</div>
				<!-- Add bank detail -->
				<h2 align="left">Bank Details</h2>

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
				<!-- End of bank details -->



				<div class="form-group">
					<?php echo Html::submitButton('Activate The Invoices & Submit', ['class'=> 'btn btn-primary','data-confirm'=> 'Are you sure want to activate the invoice and Submit']) ;?>
					<?php echo Html::a('Cancel', Yii::$app->request->referrer, ['class' => 'btn btn-warning']) ?>
				</div>

				<?php ActiveForm::end(); ?>
			</div>
		</div>
	</div>

<script>

$(document).ready(function()
{
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
		});
</script>
