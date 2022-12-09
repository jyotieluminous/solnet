<?php
use yii\helpers\Html;
use dosamigos\datepicker\DatePicker;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

?>
<h1 align="center">Disconnect Customer</h1>
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
			<div class="row">
				<div class="col-md-12 form-group">
					<div class="col-md-6">
						<label>Package Title :</label>
						<?php echo $modellinkcustpckg->package->package_title; ?>
					</div>
					<div class="col-md-6">
						<label>Package Speed :</label>
						<?php echo $modellinkcustpckg->package_speed.' '.$modellinkcustpckg->speed->speed_type; ?>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12 form-group">
					<div class="col-md-6">
						<label>Package Price :</label>
						<?php echo $modellinkcustpckg->currency->currency.' '.$modellinkcustpckg->package_price; ?>
					</div>
					<div class="col-md-6">
						<label>Other Service Fee :</label>
						<?php echo $modellinkcustpckg->other_service_fee; ?>
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
			<?php $form = ActiveForm::begin(); ?>
			<div class="row">
				<div class="col-md-12 form-group">
					<!-- <div class="col-md-4"></div> -->
					<div class="col-md-6 form-group required">
						<?php
								  $strActivationDate = strtotime($modellinkcustpckg->disconnection_date);
								  if($modellinkcustpckg->disconnection_date!='0000-00-00')
								  {
								   $modellinkcustpckg->disconnection_date = date('d-m-Y',$strActivationDate);
								  }
								  else
								  {
								    $modellinkcustpckg->disconnection_date = date('d-m-Y');
								  }
						?>
					 		<?php echo $form->field($modellinkcustpckg, 'disconnection_date')->widget(
								DatePicker::className(), [
									'clientOptions' => [
										'autoclose' => true,
										'value' => $modellinkcustpckg->disconnection_date,
										'format' => 'dd-mm-yyyy',
										'startDate'=>date('Y-m-d')
									]
							])->label();



							?>
					</div>
					<div class="col-md-6">
						<?php echo $form->field($modellinkcustpckg, 'reason_for_disconnection')->textarea(['rows' => '3','style'=>'resize:none;']) ?>
					</div>
				</div>
				<div class="form-group" align="center">
				<?php echo Html::submitButton('Submit', ['class'=> 'btn btn-primary','data-confirm'=> 'Are you sure want to disconnect this customer?']) ;?>	
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
</script>