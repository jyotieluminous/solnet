<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Customer */

$this->title = 'View Customer';
$this->params['breadcrumbs'][] = ['label' => 'Customers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-view">
    <p>
        <?php
			if(yii::$app->controller->action->id=='view'){
				echo Html::a('Customer List', ['index'], ['class' => 'btn btn-primary']) ;
				echo '&nbsp;&nbsp;'.Html::a('Add Customer', ['create'], ['class' => 'btn btn-primary']) ;
				echo '&nbsp;&nbsp;'.Html::a('Edit Customer', ['update','id'=>$model->customer_id], ['class' => 'btn btn-primary']) ;
			}
		?>
    </p>
     <p align="right">
     <?php
					if(yii::$app->controller->action->id=='view'){
							echo Html::a('<i class="fa fa-print"></i> Print', ['/customer/pdf','id'=>$model->customer_id], [
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
							<?php echo wordwrap($model->billing_address,70,"<br>\n"); ?>
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
				$roleId = Yii::$app->user->identity->fk_role_id;
				if($roleId=='21')
				{
				?>
				<div class="row">
						<div class="col-md-12">
							<div class="form-group col-md-6">
								<label>Sales agent :</label>
								<?php echo $model->agent_name; ?>
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
				<div class="row">
						<div class="col-md-12">
							<div class="form-group col-md-6">
								<label>Installation Address :</label>
								<?php echo wordwrap($model->linkcustomerpackage->installation_address,70,"<br>\n"); ?>
							</div>
							<div class="col-md-6 form-group">
								<label>Package :</label>
								<?php echo $model->linkcustomerpackage->package->package_title; ?>
							</div>
						</div>
				</div>
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
								<?php echo date_format(date_create($model->linkcustomerpackage->bulk_pay_start),'d-m-Y').' <b>To</b> '.date_format(date_create($model->linkcustomerpackage->bulk_pay_end),'d-m-Y'); ?>
							</div>
							<?php } ?>
						</div>
				</div>
				<div class="row">
						<div class="col-md-12">
							<div class="form-group col-md-6">
								<label>Installation Fee :</label>
								<?php if(!empty($model->linkcustomerpackage->installation_fee)){ echo $model->linkcustomerpackage->currency->currency.' '. number_format($model->linkcustomerpackage->installation_fee,2); } else { echo "-"; }?>
							</div>
							<div class="col-md-6 form-group">
								<label>Other Service Fee :</label>
								<?php if(!empty($model->linkcustomerpackage->other_service_fee)) { echo $model->linkcustomerpackage->currency->currency.' '. number_format($model->linkcustomerpackage->other_service_fee,2); } else { echo "-"; } ?>
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
								<?php
									if($model->invoice_send_via=='both'){
										echo 'Email,Hardcopy';
									}else{
										echo $model->invoice_send_via;
									}
								?>
							</div>
						</div>
				</div>
				
				<div class="row">
						<div class="col-md-12">
							<div class="form-group col-md-6">
								<label>Po/Wo Number :</label>
								<?php echo $model->po_wo_number; ?>
							</div>
						</div>
				</div>
				<div class="row">
						<div class="col-md-12">
							<div class="form-group col-md-6">
								<label>Additional Info :</label>
								<?php echo $model->additional_info; ?>
							</div>
						</div>
				</div>
				<?php if($model->filepath!="")
				{
				?>
				<div class="row">
					<div class="col-md-12">
							<div class="form-group col-md-6">
					<?php 
					$userDoc = $model->filepath;
					$userDoc = Url::to('@web/uploads/user_docs/'.$userDoc);
					
					echo Html::a('View uploaded document',$userDoc,['target'=>'_blank']);?>
				</div></div></div>
				<?php
				 }
				?>

</div>
</div>
		</div>
	</div>
