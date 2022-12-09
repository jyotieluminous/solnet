<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\web\NotFoundHttpException;
use yii\helpers\Url;
if(!empty($model)){
if($complain_type == 'Broadband') { 
	$link = 'broadbandjoballocationindex';
	//$updateLink = 'broadbandupdate';
	$backLink = 'customercomplain/broadbandjoballocationindex';
	//$deleteLink = 'broadbanddelete';
} else if($complain_type == 'Dedicated'){
	$link = 'dedicatedindex';
	//$updateLink = 'dedicatedupdate';
	$backLink = 'customercomplain/dedicatedindex';
	//$deleteLink = 'dedicateddelete';
} else if($complain_type == 'Local Loop'){
	$link = 'localloopindex';
	//$updateLink = 'localloopupdate';
	$backLink = 'customercomplain/localloopindex';
	//$deleteLink = 'localloopdelete';
}

$this->title = $model->ticket_number;
$this->params['breadcrumbs'][] = ['label' => $complain_type.' Job Allocations', 'url' => [$link]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="package-view">
	<p>        
	    <?php echo Html::a('Back',[$backLink],['class' => 'btn btn-default']) ?>
	</p>
	<div class="box box-default">
		<div class="box-body">
			<div class="tbllanguage-form View-Customer-sec">
				<h2 align="center">Customer Details</h2>
					<div class="row">
						<div class="col-md-12">
							<div class="col-md-6 form-group">
								<label>Customer Name :</label>
								<?php echo $model->customer->name; ?>
							</div>
							<div class="form-group col-md-6">
								<label>Customer ID :</label>
								<?php echo $model->customer->solnet_customer_id; ?>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="form-group col-md-6">
								<label>Email Address :</label>
								<?php if($model->customer->user_type=='home') {
		                    			echo $model->customer->email_address;
									} elseif($model->customer->user_type=='corporate') {
										echo $model->customer->email_it;
									} else {
										echo '-';
									} 
								?>
							</div>
							<div class="col-md-6 form-group">
								<label>Mobile No :</label>
								<?php echo $model->customer->mobile_no; ?>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">						
							<div class="col-md-6 form-group">
								<label>Installation Address	:</label>
								<?php echo $model->customer->linkcustomerpackage->installation_address;?>
							</div>
							<div class="form-group col-md-6">
								<label>Phone Number :</label>
								<?php 	if($model->customer->phone_number!='') {
		                    				echo $model->customer->phone_number;
										} else {
											echo '-';
										} 
								?>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">						
							<div class="col-md-6 form-group">
								<label>Noc Incharge	:</label>
								<?php echo $model->user->name;?>
							</div>
							<div class="form-group col-md-6">
								<label>Package Title :</label>
								<?php echo $model->customer->linkcustomerpackage->package->package_title;
								?>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">						
							<div class="col-md-6 form-group">
								<label>Package Speed :</label>
								<?php echo $model->customer->linkcustomerpackage->package_speed." ".$model->customer->linkcustomerpackage->speed->speed_type;?>
							</div>
							<div class="form-group col-md-6">
								<label>Ticket Number :</label>
								<?php echo $model->ticket_number;									
								?>
							</div>
						</div>
					</div>
					
					<div class="row">
						<div class="col-md-12">						
							<div class="col-md-6 form-group">
								<label>Complain Date Time :</label>
								<?php echo date('d-m-Y H:i A',strtotime($model->complain_date));?>
							</div>
							<div class="form-group col-md-6">
								<label>Caller Name :</label>
								<?php echo $model->caller_name;									
								?>
							</div>
						</div>
					</div>
					
					<div class="row">
						<div class="col-md-12">						
							<div class="col-md-6 form-group">
								<label>Alternative Email :</label>
								<?php if($model->alternative_email!='') {
		                    			echo $model->alternative_email;
									} else {
										echo '-';
									}?>
							</div>
							<div class="form-group col-md-6">
								<label>Alternative Phone No. 1 :</label>
								<?php echo $model->phone_no_1;									
								?>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">						
							<div class="col-md-6 form-group">
								<label>Alternative Phone No. 2 :</label>
								<?php if($model->phone_no_2!='') {
		                    			echo $model->phone_no_2;
									} else {
										echo '-';
									}
								?>
							</div>
							<div class="form-group col-md-6">
								<label>Problem :</label>
								<?php echo $model->issue;									
								?>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">						
							<div class="col-md-6 form-group">
								<label>Link Status :</label>
								<?php echo ucfirst($model->link_status);?>
							</div>
							<div class="form-group col-md-6">
								<label>Proposed Solution :</label>
								<?php echo $model->proposed_solution;									
								?>
							</div>
						</div>
					</div>				
					<div class="row">
						<div class="col-md-12">						
							<div class="col-md-6 form-group">
								<label>View Complain Documents :</label>							
								<?php
								if(!empty($model->complaindocs))
					 			{
					 				?>
						 			<ul class="upImages" id="preview-template">
						 		<?php
						 			foreach($model->complaindocs as $key=>$val)
									{
										$complainDoc = $val['filepath'];
										$complainDoc = Url::to('@web/uploads/complain_docs/'.$val['fk_complain_id'].'/'.$complainDoc);
										?>
										<li>
										<?php
										echo Html::a($val['filepath'],$complainDoc,['target'=>'_blank']);
										?>
										</li>
										<?php
									}
								}
								?>
								</ul>
							</div>
							<div class="form-group col-md-6">
								<label>Job Allocation :</label>
								<?php echo ucfirst($model->support_site);?>	
							</div>
						</div>
					</div>
			</div>

			<hr/>
			<div class="tbllanguage-form View-Customer-sec">
				<h3>Added Equipments Details</h3>
	            <h4><b>Normal Type</b></h4>
	            <hr />
	            <table class="table table-hover table-responsive EquipmentsTable">
	                <thead>
	                    <th>#</th>
	                    <th>Model Name</th>
	                    <th>Brand Name</th>
	                </thead>
	                <tbody>
	                    <?php $intCnt = 1;
	                        foreach ($arrResultEqupment as $key => $arrValue) { 
	                            //echo '<pre>';print_r($model);echo '</pre>';die;
	                        if($arrValue->euipment_type == 'normal'){
	                            foreach ($arrValue->equmentData as $arrRow) { 
	                        ?>
	                            <tr>
	                                <td><?php echo $intCnt++; ?></td>
	                                <td><?php echo $arrRow->model_type; ?></td>
	                                <td><?php echo $arrRow->brand_name; ?></td>
	                            </tr>
	                        <?php
	                            }
	                        }
	                    }
	                    ?>
	                </tbody>
	            </table>


	            <h4><b>Mac Type</b></h4>
	            <hr />
	            <table class="table  table-responsive EquipmentsTable">
	                <thead>
	                    <th>#</th>
	                    <th>Model Name</th>
	                    <th>Brand Name</th>
	                    <th>Mac Address</th>
	                </thead>
	                <tbody>
	                    <?php $intCnt = 1;
	                        foreach ($arrResultEqupment as $key => $arrValue) { 
	                        if($arrValue->euipment_type == 'mac'){
	                            foreach ($arrValue->equmentData as $arrRow) { 
	                            //echo '<pre>';print_r($arrRow);echo '</pre>';die;
	                        ?>
	                            <tr>
	                                <td><?php echo $intCnt++; ?></td>
	                                <td><?php echo $arrRow->model_type; ?></td>
	                                <td><?php echo $arrRow->brand_name; ?></td>
	                                <td class="serialNumberClass">
	                                    <?php 
	                                        foreach ($arrValue->equmentMacData as $arrMacRow) {
	                                        ?>
	                                            <span><?php echo $arrMacRow->serial_number." | "; ?></span>
	                                    <?php } ?>
	                                </td>
	                            </tr>
	                        <?php
	                            }
	                        }
	                    }
	                    ?>
	                </tbody>
	            </table>
			</div>

			<hr/>
			<div class="tbllanguage-form View-Customer-sec">
				<h3>Returned Equipments Details</h3>
	            <h4><b>Normal Type</b></h4>
	            <hr />
	            <table class="table table-hover table-responsive EquipmentsTable">
	                <thead>
	                    <th>#</th>
	                    <th>Model Name</th>
	                    <th>Brand Name</th>
	                </thead>
	                <tbody>
	                    <?php $intCnt = 1;
	                        foreach ($arrReturnEqupmentResult as $key => $arrValue) { 
	                            //echo '<pre>';print_r($model);echo '</pre>';die;
	                        if($arrValue->equipments_type == 'normal'){
	                            foreach ($arrValue->equmentData as $arrRow) { 
	                        ?>
	                            <tr>
	                                <td><?php echo $intCnt++; ?></td>
	                                <td><?php echo $arrRow->model_type; ?></td>
	                                <td><?php echo $arrRow->brand_name; ?></td>
	                            </tr>
	                        <?php
	                            }
	                        }
	                    }
	                    ?>
	                </tbody>
	            </table>


	            <h4><b>Mac Type</b></h4>
	            <hr />
	            <table class="table  table-responsive EquipmentsTable">
	                <thead>
	                    <th>#</th>
	                    <th>Model Name</th>
	                    <th>Brand Name</th>
	                    <th>Mac Address</th>
	                </thead>
	                <tbody>
	                    <?php $intCnt = 1;
	                        foreach ($arrReturnEqupmentResult as $key => $arrValue) { 
	                        if($arrValue->equipments_type == 'mac'){
	                            foreach ($arrValue->equmentData as $arrRow) { 
	                            //echo '<pre>';print_r($arrRow);echo '</pre>';die;
	                        ?>
	                            <tr>
	                                <td><?php echo $intCnt++; ?></td>
	                                <td><?php echo $arrRow->model_type; ?></td>
	                                <td><?php echo $arrRow->brand_name; ?></td>
	                                <td class="serialNumberClass">
	                                    <?php 
	                                        foreach ($arrValue->equmentMacData as $arrMacRow) {
	                                        ?>
	                                            <span><?php echo $arrMacRow->serial_number." | "; ?></span>
	                                    <?php } ?>
	                                </td>
	                            </tr>
	                        <?php
	                            }
	                        }
	                    }
	                    ?>
	                </tbody>
	            </table>
			</div>
		</div>
	</div>
</div>
<?php }else{
   throw new NotFoundHttpException('The requested page does not exist.');
   }?>
