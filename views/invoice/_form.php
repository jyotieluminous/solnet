<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Customerinvoice */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="box box-default">
		<div class="box-body">
			<div class="customerinvoice-form">
			<div class="row">
				<div class="col-md-12">
					<div class="form-group required col-md-6">
						<label>Customer Name : </label>
						<?php echo $model->customer->name; ?>
					</div>
					<div class="col-md-6 form-group required">
						<label>Customer ID : </label>
						<?php echo $model->customer->solnet_customer_id; ?>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="form-group required col-md-6">
						<label>Address : </label>
						<?php echo $model->customer->billing_address; ?>
					</div>
					<div class="col-md-6 form-group required">
						<label>Invoice No. : </label>
						<?php echo $model->invoice_number; ?>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="form-group required col-md-6">
						<label>Country : </label>
						<?php echo $model->customer->country->country; ?>
					</div>
					<div class="form-group required col-md-6">
						<label>State : </label>
						<?php echo $model->customer->state->state; ?>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="col-md-6 form-group required">
						<label>Payment type : </label>
						<?php echo $model->customer->linkcustomerpackage->payment_type; ?>
					</div>
					<div class="col-md-6 form-group required">
						<?php if($model->customer->linkcustomerpackage->payment_type=='term'){ ?>
						<label>Payment Term : </label>
						<?php echo $model->customer->linkcustomerpackage->payment_term; ?>
						<?php }elseif($model->customer->linkcustomerpackage->payment_type=='bulk'){ ?>
						<label>Payment Bulk : </label>
						<?php echo $model->customer->linkcustomerpackage->bulk_pay_start.' <b>To</b> '.$model->customer->linkcustomerpackage->bulk_pay_end; ?>
						<?php } ?>
					</div>
				</div>
			</div>
			<?php if(!empty($model->customer->po_wo_number)) { ?>
			<div class="row">
				<div class="col-md-12">
					<div class="form-group required col-md-6">
						<label>PO/ WO / Contract no : </label>
						<?php echo $model->customer->po_wo_number; ?>
					</div>
				</div>
			</div>
			<?php } ?>
    <?php $form = ActiveForm::begin(); ?>
	<div class="row">
		<div class="col-md-12">
			<div class="form-group  col-md-6">
				<?php echo $form->field($model, 'last_due_amount')->textInput() ?>
			</div>
			<div class="col-md-6 form-group required">
				<?php echo $form->field($model, 'current_invoice_amount')->textInput() ?>
			</div>
		</div>
	</div>
    <div class="row">
		<div class="col-md-12">
			<div class="form-group  col-md-6">
				<?php echo $form->field($model, 'installation_fee')->textInput() ?>
			</div>
			<div class="col-md-6 form-group ">
				<?php 
					if($model->other_service_fee==null || $model->other_service_fee=="")
					{
						$model->other_service_fee = 0;
					}
					echo $form->field($model, 'other_service_fee')->textInput() 	
				?>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<div class="form-group  col-md-6">
				<?php echo $form->field($model, 'comment_for_other_service_fee')->textarea(['rows' => '3','style'=>'resize:none;'])->label('Other Service Description') ?>
			</div>
		</div>
	</div>		
   <div class="row">
		<div class="col-md-12">
			<div class="form-group  col-md-6">
				<?php echo $form->field($model, 'total_invoice_amount')->textInput(['readOnly'=>'true']) ?>
			</div>
			<div class="form-group  col-md-6">
				<?php echo $form->field($model, 'vat')->textInput(['readOnly'=>'true']) ?>
			</div>
		</div>
	</div>
    <div class="form-group">
        <?php echo Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        <?php echo Html::a('Cancel', Yii::$app->request->referrer, ['class' => 'btn btn-warning']) ?>
    </div>

    <?php ActiveForm::end(); ?>
			</div>
		</div>
</div>
<script type="text/javascript">
$( document ).ready(function() {
		$(function(){
				var floatVat = '<?php echo $model->customer->state->vat; ?>';
				
				$("input[type='text']").blur(function (e) {
					var total = 0;
					/*$(".form-control").each(function() {
						
						if (this.readOnly) return;
						total += +this.value;
							//alert(total);
					});*/
					var lastDeu = $('#customerinvoice-last_due_amount').val();
					var currentInvoice = $('#customerinvoice-current_invoice_amount').val();
					var otherServiceFee = $('#customerinvoice-other_service_fee').val();
					var installationFee = $('#customerinvoice-installation_fee').val();
					total = +lastDeu + +currentInvoice + +otherServiceFee + +installationFee;
					var floatCalVat    = (floatVat*total)/100;
					var floatTotalInvcAmt = total + floatCalVat;
					
					$('#customerinvoice-vat').val(floatCalVat);
					$('#customerinvoice-total_invoice_amount').val(floatTotalInvcAmt);
				});
			});
});
</script>
