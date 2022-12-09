<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use dosamigos\datepicker\DatePicker;

$this->title = 'Update Customer';
?>
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
	
				

			</div>

			<div class="tbllanguage-form">
				<?php 
				$form = ActiveForm::begin([
					'options' => [
								'enctype' => 'multipart/form-data'
								],
				]);
				?>
				
				
				
				
				<div class="row">
					<div class="col-md-12">
						<div class="col-md-6 form-group required">
							<?php echo $form->field($serviceModel, 'payment_type')->dropDownList([''=>'Select Payment Type','advance' => 'PAY IN ADVANCE', 'term' => 'TERM PAYMENT', 'bulk' => 'BULK PAYMENT']); ?>
						</div>
						<div class="col-md-6 form-group required term">
							<?php echo $form->field($serviceModel, 'term_period')->textInput(['maxlength' => true]) ?>
						</div>
						
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="col-md-6 form-group required">
							<?php
							$strStartDate = strtotime($serviceModel->s_invoice_start_date);
							echo  $form->field($serviceModel, 's_invoice_start_date')->widget(
								DatePicker::className(), [
									'clientOptions' => [
										'autoclose' => true,
										'format' => 'yyyy-mm-dd',
										'startDate'=>date('Y-m-d')
									]
							]);
							?>
						</div>
					</div>
				</div>
				<div class="row picker">
					<div class="col-md-12">
						<div class="col-md-6 form-group required">
						<?php
							$strStartDate = strtotime($serviceModel->s_bulk_start_date);
							if($serviceModel->s_bulk_start_date!='0000-00-00 00:00:00')
							{
								$serviceModel->s_bulk_start_date = date('Y-m-d',$strStartDate);
							}else{
								$serviceModel->start_date = '';
							}
							
							$strEndDate = strtotime($serviceModel->s_bulk_end_date);
							if($serviceModel->s_bulk_end_date!='0000-00-00 00:00:00')
							{
								$serviceModel->s_bulk_end_date = date('Y-m-d',$strEndDate);
							}else{
								$serviceModel->s_bulk_end_date = '';
							}
						?>
							<?= $form->field($serviceModel, 's_bulk_start_date')->widget(
								DatePicker::className(), [
									'clientOptions' => [
										'autoclose' => true,
										'format' => 'yyyy-mm-dd',
										'startDate'=>date('Y-m-d')
									]
							]);?>
						</div>
						<div class="col-md-6 form-group required">
							<?= $form->field($serviceModel, 's_bulk_end_date')->widget(
								DatePicker::className(), [
									'clientOptions' => [
										'autoclose' => true,
										'format' => 'yyyy-mm-dd',
										'startDate'=>date('Y-m-d')
									]
							]);?>
						</div>
					</div>
					<div class="col-md-12">
						<div class="col-md-6 form-group required ">
							<?php echo $form->field($serviceModel, 's_bulk_price')->textInput(['maxlength' => true]) ?>
						</div>
					</div>
				</div>
			
				
				<div class="row">
					<div class="col-md-12">
						<div class="col-md-6 form-group required">
							<?php echo $form->field($model, 'vat')->textInput(['maxlength' => true,'readonly'=>true]) ?>
						</div>
						
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="col-md-6 form-group required">
							<!-- <input type="button" value="Add Service" onClick="addRow('dataTable')" /> 
							<input type="button" id = "button" value="Delete Service" onClick="deleteRow('dataTable')"  />  -->

							<button type="button" class="btn btn-success"  onClick="addRow('dataTable')"><i class="fa fa-plus"></i> <b>Add Service</b></button>
							<button type="button" class="btn btn-danger"  onClick="deleteRow('dataTable')"><i class="fa fa-minus"></i>&nbsp;<b>Delete Service</b></button> 
							<p>(The entries that are marked with checkboxes will be deleted.)</p>
						</div>
						
					</div>
				</div>
				
				<div class='row'>
					<div class="col-md-12 table-responsive">
						<table id="dataTable" class="table" >
						 <tbody>
							<?php
								if(!empty($updateService)){
									foreach($updateService as $value=>$key){ ?>
										<tr>
											<td >
											<div><input type="checkbox" name="chk[]" /></div>
											</td>
											
											<td>
												<?php echo $form->field($serviceDetailModel, 'service')->textInput(['maxlength' => true,'name' => 'service[]','value' => $key->service]) ?>
											</td>
											<td>
							
												<?php echo $form->field($serviceDetailModel, 'service_price')->textInput(['maxlength' => true,'name' => 'service_price[]' ,'value' => $key->service_price]) ?>
											</td>
											<td>
							
												<?php echo $form->field($serviceDetailModel, 'service_quantity')->textInput(['maxlength' => true,'name' => 'service_quantity[]','value' => $key->service_quantity]) ?>
											</td>
															
										</tr>
							<?php			
									}
								}
							

							?>
						
						  <tr>
						  
							
							<td >
								<div><input type="checkbox" name="chk[]" checked="checked" /></div>
							</td>
							<td>
							
							<?php echo $form->field($serviceDetailModel, 'service')->textInput(['maxlength' => true,'name' => 'service[]']) ?>
							</td>
							<td>
							
								<?php echo $form->field($serviceDetailModel, 'service_price')->textInput(['maxlength' => true,'name' => 'service_price[]']) ?>
							</td>
							<td>
							
								<?php echo $form->field($serviceDetailModel, 'service_quantity')->textInput(['maxlength' => true,'name' => 'service_quantity[]']) ?>
							</td>
							
							
						  </tr>
						 </tbody>
						</table>
					</div>
				</div>
			
				<div class="form-group">
					<?php echo Html::submitButton('Submit', ['class'=> 'btn btn-primary']) ;?>	
					<?php echo Html::a('Cancel', Yii::$app->request->referrer, ['class' => 'btn btn-warning']) ?>
				</div>
				
				<?php ActiveForm::end(); ?>
			</div>
		</div>
	</div>

<script type="text/javascript">
	$(document).ready(function() 
	{
		
		
		var strUserType = $(".code-type-radio:checked").val();
		if(strUserType=='home')  {
				$('#corporate').hide();
				$('#home').show();
			}else if(strUserType=='corporate'){
				$('#corporate').show();
				$('#home').hide();
			}else{
				$('#corporate').hide();
				$('#home').hide();
			}
		 
		//$('#corporate').hide();
		$('#customer-user_type input[type=radio]').change(function(){
    		if($(this).val()=='home')  {
				$('#corporate').hide();
				$('#home').show();
			}else{
				$('#corporate').show();
				$('#home').hide();
			}
      
      });
		
	  var intPaymentType = '<?php echo $serviceModel->payment_type; ?>';
		if(intPaymentType=='term'){
			  $('.term').show();
			  $('.bulk').hide();
			  $('.picker').hide();
		}else if(intPaymentType=='bulk'){
			  $('.picker').show();
			  $('.bulk').show();
			  $('.term').hide();
		}else{
			  $('.term').hide();
			  $('.bulk').hide();
	  		  $('.picker').hide();		
		}
		
	  
	  $('#customerservice-payment_type').change(function(){
		 
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
			  //$('#linkcustomepackage-installation_address').val('<?php //echo $modelLinkCustPackage->installation_address; ?>');
			  $('#linkcustomepackage-installation_address').val('');
		  }
	  });
	  
		
		$("#customer-vat").val('<?php echo $model->state->vat; ?>');
		
		
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
   		$('#btn-example-file-reset').on('click', function(e){
           var $el = $('#uploadFile');
           $el.wrap('<form>').closest('form').get(0).reset();
           $el.unwrap();
        });
		
	

	});
	
	
	function addRow(tableID) {
		var table = document.getElementById(tableID);
		var rowCount = table.rows.length;
		if(rowCount < 5){                            // limit the user from creating fields more than your limits
			var row = table.insertRow(rowCount);
			var colCount = table.rows[0].cells.length;
			for(var i=0; i <colCount; i++) {
				var newcell = row.insertCell(i);
				newcell.innerHTML = table.rows[0].cells[i].innerHTML;
			}
		}else{
			 alert("Maximum Passenger per ticket is 5");
				   
			}
	}

	function deleteRow(tableID) {
		var table = document.getElementById(tableID);
		var rowCount = table.rows.length;
		for(var i=0; i<rowCount; i++) {
			var row = table.rows[i];
			var inputs = row.getElementsByTagName("input");
			for (var j = 0; j < inputs.length; j++) {
			  if (inputs[j].type == "checkbox") {
				var chkbox = inputs[j];
				if(null != chkbox && true == chkbox.checked) {
					if(rowCount <= 1) {               // limit the user from removing all the fields
						alert("Cannot Remove all the Passenger.");
						break;
					}
					table.deleteRow(i);
					rowCount--;
					i--;
				}	
				
				
			  }
			}
			
		}
	}
</script>