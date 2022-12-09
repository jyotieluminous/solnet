<?php
use yii\helpers\Html;
use dosamigos\datepicker\DatePicker;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

?>
<h1 align="center">Reactivate Customer</h1>
<div class="box box-default">
	<div class="box-body">
		<div class="tbllanguage-form">
			<div class="row">
				<div class="col-md-12 form-group">
					<div class="col-md-6">
						<label>Customer ID :</label>
						<?php echo $model->solnet_customer_id; ?>
					</div>
					<div class="col-md-6">
						<label>Name :</label>
						<?php echo $model->name; ?>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12 form-group">
					<div class="col-md-6">
						<label>Address :</label>
						<?php echo $model->billing_address; ?>
					</div>
					<div class="col-md-6">
						<label>State :</label>
						<?php echo $model->state->state; ?>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12 form-group">
					<div class="col-md-6">
						<label>Country :</label>
						<?php echo $model->country->country; ?>
					</div>
					<div class="col-md-6">
						<label>Mobile No. :</label>
						<?php echo $model->mobile_no; ?>
					</div>
				</div>
			</div>
			<?php 
				$form = ActiveForm::begin([
					'options' => [
								'enctype' => 'multipart/form-data'
								],
				]);
				?>
			
			<div class="row">
				<div class="col-md-12 form-group">
					<div class="col-md-6">
						<label>Other Service Fee :</label>
						<?php echo $modellinkcustpckg->other_service_fee; ?>
					</div>
					<div class="col-md-6">
						<label>Currency :</label>
						<?php echo $modellinkcustpckg->currency->currency; ?>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12 form-group">
					<div class="col-md-6">
						<label>Contract Start Date :</label>
						<?php echo date('d-m-Y',strtotime($modellinkcustpckg->contract_start_date)); ?>
					</div>
					<div class="col-md-6">
						<label>Contract End Date :</label>
						<?php echo date('d-m-Y',strtotime($modellinkcustpckg->contract_end_date)); ?>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12 form-group">
					<div class="col-md-6">
						<?php echo $form->field($modellinkcustpckg, 'fk_package_id')->dropDownList(
								$packageList,['prompt'=>'Select Package']); ?>
					</div>
					<div class="col-md-6">
						<?php echo $form->field($modellinkcustpckg, 'package_price')->textInput(); ?>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="col-md-6">
							<?php echo $form->field($modellinkcustpckg, 'fk_speed_id')->dropDownList(
									$speedList,['prompt'=>'Select Speed']); ?>
					</div>
				</div>
			</div>
			<div class="row">
					<div class="col-md-12">
						<div class="col-md-6 form-group required">
							<?php echo $form->field($modellinkcustpckg, 'payment_type')->dropDownList([''=>'Select Payment Type','advance' => 'PAY IN ADVANC', 'term' => 'TERM PAYMENT', 'bulk' => 'BULK PAYMENT']); ?>
						</div>
						<div class="col-md-6 form-group required term">
							<?php echo $form->field($modellinkcustpckg, 'payment_term')->textInput(['maxlength' => true]) ?>
						</div>
					</div>
				</div>
				<div class="row picker">
					<div class="col-md-12">
						<div class="col-md-6 form-group required">
						<?php
							$strStartDate = strtotime($modellinkcustpckg->bulk_pay_start);
							if($modellinkcustpckg->bulk_pay_start!='0000-00-00 00:00:00')
							{
								$modellinkcustpckg->start_date = date('d-m-Y',$strStartDate);
							}else{
								$modellinkcustpckg->start_date = '';
							}
							
							$strEndDate = strtotime($modellinkcustpckg->bulk_pay_end);
							if($modellinkcustpckg->bulk_pay_end!='0000-00-00 00:00:00')
							{
								$modellinkcustpckg->end_date = date('d-m-Y',$strEndDate);
							}else{
								$modellinkcustpckg->end_date = '';
							}
						?>
							<?= $form->field($modellinkcustpckg, 'start_date')->widget(
								DatePicker::className(), [
									'clientOptions' => [
										'autoclose' => true,
										'format' => 'dd-mm-yyyy',
										//'startDate'=>date('Y-m-d')
									]
							]);?>
						</div>
						<div class="col-md-6 form-group required">
							<?= $form->field($modellinkcustpckg, 'end_date')->widget(
								DatePicker::className(), [
									'clientOptions' => [
										'autoclose' => true,
										'format' => 'dd-mm-yyyy',
										//'startDate'=>date('Y-m-d')
									]
							]);?>
						</div>
					</div>
				</div>
			<div class="row">
				<div class="col-md-12 form-group">
					<div class="col-md-6">
						<?php
								  $strInvoiceStartDate = strtotime($modellinkcustpckg->invoice_start_date);
								  if($modellinkcustpckg->invoice_start_date!='0000-00-00 00:00:00')
								  {
								   $modellinkcustpckg->invoice_start_date = date('d-m-Y',$strInvoiceStartDate);
								  }
								  else
								  {
								    $modellinkcustpckg->invoice_start_date = '';
								  }
						?>
					 		<?php echo $form->field($modellinkcustpckg, 'invoice_start_date')->widget(
								DatePicker::className(), [
									'clientOptions' => [
										'autoclose' => true,
										'format' => 'dd-mm-yyyy',
										//'startDate'=>date('Y-m-d')
									]
							])->label();?>
					</div>
					
					<div class="col-md-6">
							<?php
										  $strReActivationDate = strtotime($modellinkcustpckg->reactivation_date);
										  if($modellinkcustpckg->reactivation_date!='0000-00-00 00:00:00')
										  {
										   $modellinkcustpckg->reactivation_date = date('d-m-Y',$strReActivationDate);
										  }
										  else
										  {
											$modellinkcustpckg->reactivation_date = '';
										  }
								?>
								<?php echo $form->field($modellinkcustpckg, 'reactivation_date')->widget(
										DatePicker::className(), [
											'clientOptions' => [
												'autoclose' => true,
												'format' => 'dd-mm-yyyy',
												//'startDate'=>date('Y-m-d')
											]
									])->label();
								?>	
						
					</div>
				</div>
				<div class="form-group" align="center">
				<?php echo Html::submitButton('Submit', ['class'=> 'btn btn-primary','data-confirm'=> 'Are you sure want to reactivate this customer?']) ;?>	
				<?php echo Html::button('Cancel', ['class'=> 'btn btn-default closemodal']) ;?>
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
	
	var intPaymentType = '<?php echo $model->linkcustomerpackage->payment_type; ?>';
		if(intPaymentType=='term'){
			  $('.term').show();
			  $('.picker').hide();
		}else if(intPaymentType=='bulk'){
			  $('.picker').show();
			  $('.term').hide();
		}else{
			  $('.term').hide();
	  		  $('.picker').hide();		
		}
		
	  
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
</script>