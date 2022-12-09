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
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<div class="customer-view">
    <p>
        <?php
			if(yii::$app->controller->action->id=='view'){
				echo Html::a('Customer List', ['index'], ['class' => 'btn btn-primary']) ;
				echo '&nbsp;&nbsp;'.Html::a('Add Customer', ['create'], ['class' => 'btn btn-primary']) ;
				echo '&nbsp;&nbsp;'.Html::a('Edit Customer', ['update','id'=>$model->customer_id], ['class' => 'btn btn-primary']) ;
				echo '&nbsp;&nbsp;'.Html::a('Add Equipments', ['addequipment','id'=>$model->customer_id], ['class' => 'btn btn-primary']) ;
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
							<?php 
							if(Yii::$app->user->identity->fk_role_id=='8' || Yii::$app->user->identity->fk_role_id=='23' || Yii::$app->user->identity->fk_role_id=='24' || Yii::$app->user->identity->fk_role_id=='25')
							{
							?>
								<div class="col-md-6 form-group">
									
								</div>	
							<?php 
							}else{
							?>
								<div class="col-md-6 form-group">
									<label>Package Price :</label>
									<?php echo $model->linkcustomerpackage->currency->currency.' '.number_format($model->linkcustomerpackage->package_price,2); ?>
								</div>
							<?php 
							} ?>
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
	<div class="box-body">
			<div class="tbllanguage-form View-Customer-sec">
				<?php if(Yii::$app->session->hasFlash('success_msg')) : ?>
		            <div class="alert-success alert fade in" style="width: 50%">
		                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
		                    <?php echo Yii::$app->session->getFlash('success_msg'); ?>
		            </div>
	 			<?php endif; ?>
				<h2 align="center">Equipments Details</h2>
				<hr />
				<table class="table table-hover table-responsive" id="EquipmentsTable">
				 	<thead>
				 		<th>#</th>
				 		<th>Model Name</th>
				 		<th>Brand Name</th>
				 		<th>Quantity</th>
				 		<th>Mac Address</th>
				 		<th>Action</th>
				 	</thead>
				 	<tbody>
				 		<?php $intCnt = 1;
				 			foreach ($arrResultEqupment as $key => $arrValue) { 
				 				foreach ($arrValue->equmentData as $arrRow) { 
				 			?>
				 				<tr>
				 					<td><?php echo $intCnt++; ?></td>
				 					<td><?php echo $arrRow->model_type; ?></td>
				 					<td><?php echo $arrRow->brand_name; ?></td>
				 					<td><?php echo $arrValue->quantity; ?></td>
				 					<td>
				 						<?php if($arrValue->euipment_type == 'mac'){ 
				 							foreach ($arrValue->equmentMacData as $arrMacRow) { 
				 								if($arrMacRow->serial_number != ''){ ?>
				 									<span><?php echo $arrMacRow->serial_number," | "; ?></span>
				 								<?php }else{ ?>
				 									<span><?php echo $arrMacRow->mac_address," | "; ?></span>
				 						<?php 	}
				 							}
				 						}else{ ?>
				 							<span>-</span>
				 						<?php
				 						} ?>
				 					</td>
				 					<td>
				 						<?php echo Html::a('<i class="fa fa-trash deleteBtn"></i>', ['/customer/deleteequipment','id'=>$arrValue->id],['title'=>'Delete Equipments','onclick'=>"return confirm('Are you sure you want to delete this item?')"]); ?>
				 					</td>
				 				</tr>
				 		<?php
				 				}
				 			}
				 		?>
				 	</tbody>
				</table>
			</div>
		</div>
</div>
</div>
<div class="box box-default">

</div>
<script type="text/javascript">
	$(document).ready(function() {
	    $('#EquipmentsTable').DataTable();
	} );
</script>