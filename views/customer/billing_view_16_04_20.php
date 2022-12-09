<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Customer */

$this->title = 'View Customer';
$this->params['breadcrumbs'][] = ['label' => 'Billing Customers', 'url' => ['billing']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-view">
    <p>
        <?php
			if(yii::$app->controller->action->id=='view'){
				echo Html::a('Pending Installation', ['index'], ['class' => 'btn btn-primary']) ;
				echo '&nbsp;&nbsp;'.Html::a('Add Customer', ['create'], ['class' => 'btn btn-primary']) ;
				echo '&nbsp;&nbsp;'.Html::a('Edit Customer', ['update','id'=>$model->customer_id], ['class' => 'btn btn-primary']) ;
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
					
						<div class="col-md-6 form-group">
							<label>Installation Address :</label>
							<?php echo $model->linkcustomerpackage->installation_address; ?>
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
						<div class="form-group col-md-4">
								<label>User File :</label>
								<?php
									if(!empty($model->filepath))
									{
											echo Html::a("<img src=".Yii::getAlias('@web')."/web/images/pdf.png width=50px height=50px >", ['download', 'strFileName' => $model->filepath]) ;
									}
								?>
						</div>
					</div>
				</div>
				<h2 align="center">Package Details</h2>

				<div class="row">
						<div class="col-md-12">
							<div class="form-group col-md-10">
								<label>Package Title :</label>
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
						<?php	if(Yii::$app->user->identity->fk_role_id!='8'){ ?>
							<div class="col-md-6 form-group">
								<label>Package Price :</label>
								<?php echo $model->linkcustomerpackage->currency->currency.' '.number_format($model->linkcustomerpackage->package_price,2); ?>
							</div>
							<?php } ?>
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
								<label>VAT :</label>
								<?php echo $model->state->vat.'%'; ?>
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
								<label>Invoice Send Via :</label>
								<?php echo $model->invoice_send_via; ?>
							</div>
							<div class="col-md-6 form-group">
								<label>Invoice Start Date :</label>
								<?php echo date('d-m-Y',strtotime($model->linkcustomerpackage->invoice_start_date)); ?>
							</div>
						</div>
				</div>



				<div class="row">
						<div class="col-md-12">
							<div class="form-group col-md-6">
								<label>Contract Start Date :</label>
								<?php echo date('d-m-Y',strtotime($model->linkcustomerpackage->contract_start_date)); ?>
							</div>
							<div class="col-md-6 form-group">
								<label>Contract End Date :</label>
								<?php echo date('d-m-Y',strtotime($model->linkcustomerpackage->contract_end_date)); ?>
							</div>
						</div>
				</div>

        <div class="row">
            <div class="col-md-12">
              <div class="form-group col-md-6">
                <label>Installation Date :</label>
                <?php if((($model->linkcustomerpackage->activation_date)=='0000-00-00 00:00:00') || empty($model->linkcustomerpackage->activation_date)){
                		echo '--';
                	}
                	else{
                		echo date('d-m-Y',strtotime($model->linkcustomerpackage->activation_date));
                	}
                		?>
              </div>
              <div class="col-md-6 form-group">
                <label>Installation Address :</label>
                <?php echo $model->linkcustomerpackage->installation_address; ?>
              </div>
            </div>
        </div>

         <div class="row">
            <div class="col-md-12">
              <div class="form-group col-md-6">
				 
                <label>Po Wo Number :</label>
                <?php if(empty($model->po_wo_number)){
                		echo '--';
                	}
                	else{
                		echo $model->po_wo_number;
                	}
                		?>
				</div>
				<div class="form-group col-md-6">
                <label>Contract Number :</label>
                <?php if(empty($model->linkcustomerpackage->contract_number)){
                		echo '--';
                	}
                	else{
                		echo $model->linkcustomerpackage->contract_number;
                	}
                		?>
              </div>
              
            </div>
        </div>
		
		<div class="row">
						<div class="col-md-12">
							<div class="form-group col-md-10">
								<label>Additional Info :</label>
							
								<?php if(empty($model->additional_info)){
									echo '--';
								}
								else{
									echo $model->additional_info;
								}
									?>
							</div>
						</div>
		</div>

				<h2 align="center">Bank Details</h2>
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
				<?php } ?>



</div>
</div>
		</div>
	</div>
