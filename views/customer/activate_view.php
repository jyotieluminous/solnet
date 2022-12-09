<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Customer */

$this->title = 'Activate Customer View';
$this->params['breadcrumbs'][] = ['label' => 'Manage pending activations', 'url' => ['customer/pending']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-view">
    <p>
        <?php
			if(yii::$app->controller->action->id=='activateview'){
				echo Html::a('Manage Pending Activations', ['customer/pending'], ['class' => 'btn btn-primary']) ;
				if(!empty($model->is_invoice_activated=='no'))
				{
					echo '&nbsp;&nbsp;'.Html::a('Activate Customer', ['customer/activate','id'=>$model->customer_id], ['class' => 'btn btn-primary']) ;
				}
			}

		?>
    </p>
    <p align="right">
    	<?php
					if(yii::$app->controller->action->id=='billview'){
							echo Html::a('<i class="fa fa-print"></i> Print', ['/customer/billpdf','id'=>$model->customer_id], [
							'class'=>'btn btn-danger',
							'data-toggle'=>'tooltip',
							'title'=>'Will open the generated PDF file in a new window',
							'target'=>'_blank'
						]);
					}
				?>
    </p>

<div class="box box-default">
		<div class="box-body">
			<div class="tbllanguage-form View-Customer-sec">
				<h2 align="center">Customer Details</h2>
				<div class="row">
					<div class="col-md-12">
						<div class="col-md-6 form-group">
							<label>Name :</label>
							<?php echo ucfirst($model->name); ?>
						</div>
						<div class="form-group col-md-6">
							<label>User type :</label>
							<?php echo ucfirst($model->user_type); ?>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="form-group col-md-6">
							<label>KTP / SIM / Passport No :</label>
							<?php echo $model->ktp_pass_no; ?>
						</div>
						<div class="col-md-6 form-group">
							<label>Billing Address :</label>
							<?php echo $model->billing_address; ?>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="form-group col-md-6">
							<label>Country :</label>
							<?php echo ucfirst($model->country->country); ?>
						</div>
						<div class="col-md-6 form-group">
							<label>State :</label>
							<?php echo ucfirst($model->state->state); ?>
						</div>
					</div>
				</div>
				<?php
				if($model->user_type=='home')
				{
				?>
				<div class="row">
					<div class="col-md-12">
						<div class="form-group col-md-6">
							<label>Email :</label>
							<?php echo $model->email_address; ?>
						</div>
					</div>
				</div>
				<?php
				}
				else{
					?>
					<div class="row">
						<div class="col-md-12">
							<div class="form-group col-md-6">
								<label>Email Finance :</label>
								<?php echo $model->email_finance; ?>
							</div>
							<div class="col-md-6 form-group">
								<label>Email IT Incharge :</label>
								<?php echo $model->email_it; ?>
							</div>
						</div>
				</div>
			<?php
				}
				?>
				<div class="row">
						<div class="col-md-12">
							<div class="form-group col-md-6">
								<label>Mobile No. :</label>
								<?php echo $model->mobile_no; ?>
							</div>
							<div class="col-md-6 form-group">
								<label>Fixed Line No :</label>
								<?php echo $model->fixed_line_no; ?>
							</div>
						</div>
				</div>
				<h2 align="center">Package Details</h2>

				<div class="row">
						<div class="col-md-12">
							<div class="form-group col-md-6">
								<label>Package Speed :</label>
								<?php echo $model->linkcustomerpackage->package_speed.' '.$model->linkcustomerpackage->speed->speed_type; ?>
							</div>
							<div class="col-md-6 form-group">
								<label>Package Price :</label>
								<?php echo $model->linkcustomerpackage->currency->currency.' '.number_format($model->linkcustomerpackage->package_price,2); ?>
							</div>
						</div>
				</div>
				<div class="row">
						<div class="col-md-12">
							<div class="form-group col-md-6">
								<label>Payment Type :</label>
								<?php echo ucfirst($model->linkcustomerpackage->payment_type); ?>
							</div>
							<?php if($model->linkcustomerpackage->payment_type=='term') { ?>
							<div class="col-md-6 form-group">
								<label>Payment Term :</label>
								<?php echo $model->linkcustomerpackage->payment_term.'(days)'; ?>
							</div>
							<?php } elseif($model->linkcustomerpackage->payment_type=='bulk'){?>
							<div class="col-md-6 form-group">
								<label>Start & End Date:</label>
								<?php echo date_format(date_create($model->linkcustomerpackage->bulk_pay_start),'Y-m-d').' <b>To</b> '.date_format(date_create($model->linkcustomerpackage->bulk_pay_end),'Y-m-d'); ?>
							</div>
							<?php } ?>
						</div>
				</div>
				<div class="row">
						<div class="col-md-12">
							<div class="form-group col-md-6">
								<label>Installation Fee :</label>
								<?php echo$model->linkcustomerpackage->currency->currency.' '.number_format($model->linkcustomerpackage->installation_fee,2); ?>
							</div>
							<div class="col-md-6 form-group">
								<label>Other Service Fee :</label>
								<?php echo $model->linkcustomerpackage->currency->currency.' '.number_format($model->linkcustomerpackage->other_service_fee,2); ?>
							</div>
						</div>
				</div>
				<div class="row">
						<div class="col-md-12">
							<div class="form-group col-md-6">
								<label>VAT :</label>
								<?php echo $model->state->vat.'%'; ?>
							</div>
							<div class="col-md-6 form-group">
								<label>Invoice Send Via :</label>
								<?php echo $model->invoice_send_via; ?>
							</div>
						</div>
				</div>

				<?php /*?><h2 align="center">Bank Details</h2>
				<div class="row">
						<div class="col-md-12">
							<div class="form-group col-md-6">
								<label>Is Solnet Bank :</label>
								<?php echo ucfirst($model->linkcustomerpackage->is_solnet_bank); ?>
							</div>
						</div>
				</div>
				<?php
					if($model->linkcustomerpackage->is_solnet_bank=='no')
					{
				?>
				<div class="row">
					<div class="col-md-12">
						<div class="col-md-4">
							<label>Bank Name :</label>
							<?php echo $model->linkcustomerpackage->bank_name; ?>
						</div>
						<div class="col-md-4">
							<label>Virtual Account No. :</label>
							<?php echo $model->linkcustomerpackage->virtual_acc_no; ?>
						</div>
						<div class="col-md-4">
							<label>Account Name :</label>
							<?php echo $model->linkcustomerpackage->account_name; ?>
						</div>
					</div>
				</div>
				<?php }elseif($model->linkcustomerpackage->is_solnet_bank=='yes'){ ?>
				<div class="row">
					<div class="col-md-12">
						<div class="col-md-4">
							<label>Bank Name :</label>
							<?php echo $model->linkcustomerpackage->bank->bank_name; ?>
						</div>
						<div class="col-md-4">
							<label>Account No. :</label>
							<?php echo $model->linkcustomerpackage->bank->account_no; ?>
						</div>
						<div class="col-md-4">
							<label>Account Name :</label>
							<?php echo $model->linkcustomerpackage->bank->account_name; ?>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="col-md-4">
							<label>Bank Branch :</label>
							<?php echo $model->linkcustomerpackage->bank->bank_branch; ?>
						</div>
						<div class="col-md-4">
							<label>Currency :</label>
							<?php echo $model->linkcustomerpackage->bank->currency->currency; ?>
						</div>
					</div>
				</div>
				<?php } ?><?php */?>



</div>
</div>
		</div>
	</div>
