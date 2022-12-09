<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use dosamigos\datepicker\DatePicker;
use demogorgorn\ajax\AjaxSubmitButton;


$this->title = 'Edit Customer';
$this->params['breadcrumbs'][] = ['label' => 'Billing Customers', 'url' => ['billing']];
$this->params['breadcrumbs'][] = $this->title;
$strActive1 = '';
$strActive2 = '';
$strActive3 = '';
if(isset($_GET['tab']) && !empty($_GET['tab']))
{
	if($_GET['tab']=='package'){
		$strActive2 = 'active';
		
	}elseif($_GET['tab']=='bank'){
		$strActive3 = 'active';
	}else{
		$strActive1 = 'active';
	}
}else{
	$strActive1 = 'active';
}
?>
<div class="row">
        <div class="col-md-12">
          <!-- Custom Tabs -->
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="<?php echo $strActive1; ?>"><a href="#tab_1" data-toggle="tab">Personal Details</a></li>
              <li class="<?php echo $strActive2; ?>"><a href="#tab_2" data-toggle="tab">Package Details</a></li>
              <li class="<?php echo $strActive3; ?>"><a href="#tab_3" data-toggle="tab">Bank Details</a></li>
            </ul>
            <div class="tab-content">
              <div class="tab-pane <?php echo $strActive1; ?>" id="tab_1">
				<?php 
				$form = ActiveForm::begin([
					'options' => [
								'enctype' => 'multipart/form-data'
								],
				]);
				echo Html::hiddenInput('tab', 'personal'); 
				?>
				<div class="row">
					<div class="col-md-12">
						<div style="display: none;" class="alert alert-success" id="success_personal"></div>
					</div>	
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="form-group required col-md-6">
						
							<?php echo $form->field($model, 'user_type')->radioList(array('home'=>'Home','corporate'=>'Corporate'),['itemOptions' => ['class' =>'code-type-radio']]); ?>
						</div>
						<div class="col-md-6 form-group required">
							<?php echo $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="col-md-6 form-group required">
							<?php echo $form->field($model, 'ktp_pass_no')->textInput(['maxlength' => true]) ?>
						</div>
						<div class="col-md-6 form-group required">
							<?php echo $form->field($model, 'billing_address')->textArea(['maxlength' => true]) ?>
						</div>
						
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="col-md-6 form-group required">
							<?php echo $form->field($model, 'customer_type')->dropDownList(
								['Broadband'=>'Broadband','Dedicated'=>'Dedicated','Local Loop'=>'Local Loop'],['prompt'=>'Select Customer Type']); //['prompt'=>'Select Country'] ?>
						</div>
						<div class="col-md-6 form-group required">
							<?php echo $form->field($model, 'fiber_installed')->dropDownList(
							[''=>'All','power'=>'Power','dig'=>'DIG','FTTH'=>'Wireless'],['prompt'=>'Select Fiber Installed']); //['prompt'=>'Select Country'] ?>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="col-md-6 form-group required">
							<?php echo $form->field($model, 'fk_country_id')->dropDownList(
								$countryList,['prompt'=>'Select Country','onchange'=>'$.post( "'.Yii::$app->urlManager->createUrl('customer/state?id=').'"+$(this).val(), function( data ) { $( "select#customer-fk_state_id" ).html( data );});']); ?>
						</div>
						<div class="col-md-6 form-group required">
							<?php echo $form->field($model, 'fk_state_id')->dropDownList(
								$stateList,['prompt'=>'Select State']); ?>
						</div>
					</div>
				</div>
				<div class="row" >
					<div class="col-md-12">
						<div class="col-md-6 form-group required" id="home">
							<?php echo $form->field($model, 'email_address')->textInput(['maxlength' => true]) ?>
						</div>
						<?php
							$roleId = Yii::$app->user->identity->fk_role_id;
							if($roleId=='21')
							{
						?>
						<div class="col-md-6 form-group">
							<?php echo $form->field($model, 'agent_name')->textInput(['maxlength' => true]) ?>
						</div>
						<?php
							}
						?>
					</div>
				</div>
				<div class="row" id="corporate">
					<div class="col-md-12">
						<div class="col-md-6 form-group required">
							<?php echo $form->field($model, 'email_it')->textInput(['maxlength' => true]) ?>
						</div>
						<div class="col-md-6 form-group">
							<?php echo $form->field($model, 'email_finance')->textInput(['maxlength' => true]) ?>
						</div>
					</div>
					
					<div class="col-md-12">
						<div class="col-md-6 form-group required">
							<?php echo $form->field($model, 'it_pic')->textInput(['maxlength' => true]) ?>
						</div>
					</div>
					
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="col-md-6 form-group ">
							<?php echo $form->field($model, 'optional_email')->textInput(['maxlength' => true]) ?>
						
						</div>
					</div>
				</div>
				
				
				<div class="row">
					<div class="col-md-12">
						<div class="col-md-6 form-group required">
							<?php echo $form->field($model, 'mobile_no')->textInput(['maxlength' => true]) ?>
							<em>(Mobile No. should start with + sign)</em>
						</div>
						<div class="col-md-6 form-group">
							<?php echo $form->field($model, 'fixed_line_no')->textInput(['maxlength' => true]) ?>
							<em>(Fixed line No. should start with + sign)</em>
						</div>
					</div>
				</div>
             	<div class="row">
					<div class="col-md-12">
						<div class="col-md-6 form-group ">
							<?php echo $form->field($model, 'po_wo_number')->textInput(['maxlength' => true]) ?>
						</div>
						<div class="col-md-6 form-group ">
							<?php echo $form->field($model, 'additional_info')->textInput(['maxlength' => true]) ?>
						</div>
						<div class="col-md-6 form-group">
							<?php echo $form->field($model, 'enable_disconnection')->checkbox(['maxlength' =>true]) ?>
							
						</div>
						<div class="col-md-6 form-group">
							<?php echo $form->field($model, 'microtik_ip')->textInput(['maxlength' =>true]) ?>
							
						</div>
					</div>
				</div>
              	<div  class="row">
              		<div class="col-md-12">
						<div class="col-md-6 form-group">
							<?php echo $form->field($modelLinkCustPackage, 'installation_address')->textArea(['maxlength' => true]) ?>
							<?php if($model->is_address_same=='yes') { $model->is_address_same =1; } ?>
							<?php echo $form->field($model, 'is_address_same')->checkbox() ?>
						</div>
						<div class="col-md-6 form-group required">
							<?php echo $form->field($model, 'invoice_send_via')->radioList(array('email'=>'Email','hardcopy'=>'Hardcopy','both'=>'Both')); ?>
						</div>
					</div>
              	</div>
              	<div class="row">
					<div class="col-md-12">
						<div class="col-md-6 form-group  one-line-bx">
							<?php echo $form->field($model, 'filepath')->fileInput(['maxlength' => true,'id' => 'uploadFile']) ?>
							<?php
								if(!empty($model->filepath))
								{
									echo Html::a("<img src=".Yii::getAlias('@web')."/web/images/pdf.png width=50px height=50px >", ['download', 'strFileName' => $model->filepath], ['style' => 'margin: -70px 0px 0px 180px;']) ;
								}
							?>
								<button id="btn-example-file-reset" type="button">Reset file</button>
						</div>
						<div class="col-md-6 form-group required">
							<?php echo $form->field($model, 'vat')->textInput(['maxlength' => true,'readonly'=>true]) ?>
						</div>
					</div>
				</div>	
               <div class="form-group">
               		<?php echo Html::submitButton('Submit', ['class'=> 'btn btn-primary']) ;?>	
               		<?php echo Html::a('Cancel', Yii::$app->request->referrer, ['class' => 'btn btn-warning']) ?>	
				</div>
				
				<?php ActiveForm::end(); ?>
                
              </div>
              <!-- /.tab-pane -->
              <div class="tab-pane <?php echo $strActive2; ?>" id="tab_2">
               <div class="row">
					<div class="col-md-12">
						<div style="display: none;" class="alert alert-success" id="success_package"></div>
					</div>	
				</div>
                <?php 
				$form = ActiveForm::begin([
					'options' => [
								'enctype' => 'multipart/form-data'
								],
				]);
				 echo Html::hiddenInput('tab', 'package'); 
				?>
             	<div class="row">
					<div class="col-md-12">
						<div class="col-md-6 form-group required">
							<?php echo $form->field($modelLinkCustPackage, 'fk_package_id')->dropDownList(
								$packageList,['prompt'=>'Select Package']); ?>
						</div>
						
						<div class="col-md-6 form-group ">
							<?php echo $form->field($modelLinkCustPackage, 'bundling_package')->textArea(['maxlength' => false,'id' =>'editor']); ?>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="col-md-6 form-group required">
							<?php echo $form->field($modelLinkCustPackage, 'package_speed')->textInput(['maxlength' => true]) ?>
							
						</div>
						<div class="col-md-6 form-group required">
								<?php echo $form->field($modelLinkCustPackage, 'fk_speed_id')->dropDownList(
								$speedList,['prompt'=>'Select Speed']); ?>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="col-md-6 form-group required">
							<?php echo $form->field($modelLinkCustPackage, 'package_price')->textInput(['maxlength' => true]) ?>
						</div>
						<div class="col-md-6 form-group required">
							<?php echo $form->field($modelLinkCustPackage, 'fk_currency_id')->dropDownList(
								$currencyList,['prompt'=>'Select Currency']); ?>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="col-md-6 form-group required">
							<?php echo $form->field($modelLinkCustPackage, 'payment_type')->dropDownList([''=>'Select Payment Type','advance' => 'PAY IN ADVANC', 'term' => 'TERM PAYMENT', 'bulk' => 'BULK PAYMENT']); ?>
						</div>
						<div class="col-md-6 form-group required term">
							<?php echo $form->field($modelLinkCustPackage, 'payment_term')->textInput(['maxlength' => true]) ?>
						</div>
						<div class="col-md-6 form-group required bulk">
							<?php echo $form->field($modelLinkCustPackage, 'bulk_price')->textInput(['maxlength' => true]) ?>
						</div>
						<div class="col-md-6 form-group required pay_in_advanc_grace_period">
							<?php echo $form->field($modelLinkCustPackage, 'pay_in_advanc_grace_period')->textInput(['maxlength' => true])->label('PAY IN ADVANC Grace period (in days):') ?>
						</div>
						<div class="col-md-6 form-group required bulk_grace_period">
							<?php echo $form->field($modelLinkCustPackage, 'bulk_grace_period')->textInput(['maxlength' => true])->label('BULK PAYMENT Grace period (in days ):') ?>
						</div>
						<div class="col-md-6 form-group required term_grace_period">
							<?php echo $form->field($modelLinkCustPackage, 'term_grace_period')->textInput(['maxlength' => true])->label('TERM PAYMENT Grace period (in days ):') ?>
						</div>
					</div>
				</div>
				<div class="row picker">
					<div class="col-md-12">
						<div class="col-md-6 form-group required">
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
							<?= $form->field($modelLinkCustPackage, 'start_date')->widget(
								DatePicker::className(), [
									'clientOptions' => [
										'autoclose' => true,
										'format' => 'dd-mm-yyyy',
										//'startDate'=>date('Y-m-d')
									]
							]);?>
						</div>
						<div class="col-md-6 form-group required">
							<?= $form->field($modelLinkCustPackage, 'end_date')->widget(
								DatePicker::className(), [
									'clientOptions' => [
										'autoclose' => true,
										'format' => 'dd-mm-yyyy',
										//'startDate'=>date('Y-m-d')
									],
							]);?>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="col-md-6 form-group">
							<?php echo $form->field($modelLinkCustPackage, 'installation_fee')->textInput(['maxlength' => true]) ?>
						</div>
						<div class="col-md-6 form-group">
							<?php echo $form->field($modelLinkCustPackage, 'other_service_fee')->textInput(['maxlength' => true]) //,'readonly'=>true ?>
						</div>
						
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="col-md-6 form-group">
							<?php echo $form->field($modelLinkCustPackage, 'contract_number')->textInput(['maxlength' => true]) ?>
						</div>
						<div class="col-md-6 form-group required">
						<?php
							$strInvoiceStartDate = strtotime($modelLinkCustPackage->invoice_start_date);
							if($modelLinkCustPackage->invoice_start_date!='0000-00-00 00:00:00')
							{
								$modelLinkCustPackage->invoice_start_date = date('d-m-Y',$strInvoiceStartDate);
							}else{
								$modelLinkCustPackage->invoice_start_date = '';
							}
						?>
							<?= $form->field($modelLinkCustPackage, 'invoice_start_date')->widget(
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
					<div class="col-md-12">
						<div class="col-md-6 form-group required">
						<?php
							$strContrStartDate = strtotime($modelLinkCustPackage->contract_start_date);
							if($modelLinkCustPackage->contract_start_date!='0000-00-00 00:00:00')
							{
								$modelLinkCustPackage->contract_start_date = date('d-m-Y',$strContrStartDate);
							}else{
								$modelLinkCustPackage->contract_start_date = '';
							}
							
							$strContrEndDate = strtotime($modelLinkCustPackage->contract_end_date);
							if($modelLinkCustPackage->contract_end_date!='0000-00-00 00:00:00')
							{
								$modelLinkCustPackage->contract_end_date = date('d-m-Y',$strContrEndDate);
							}else{
								$modelLinkCustPackage->contract_end_date = '';
							}
						?>
							<?= $form->field($modelLinkCustPackage, 'contract_start_date')->widget(
								DatePicker::className(), [
									'clientOptions' => [
										'autoclose' => true,
										'format' => 'dd-mm-yyyy',
										//'startDate'=>date('d-m-Y')
									]
							]);?>
						</div>
						<div class="col-md-6 form-group required">
							<?= $form->field($modelLinkCustPackage, 'contract_end_date')->widget(
								DatePicker::className(), [
									'clientOptions' => [
										'autoclose' => true,
										'format' => 'dd-mm-yyyy',
										//'startDate'=>date('d-m-Y')
									],
							]);?>
						</div>
					</div>
				</div>
				
				
				
				<div class="form-group">
				 <?php //echo Html::submitButton('Submit', ['class'=> 'btn btn-primary']) ;?>	
					<?php
					AjaxSubmitButton::begin([
						'label' => 'Save Package Details',
						'ajaxOptions' => [
							'type'=>'POST',
							'url'=>Url::to(['customer/plan','id'=>$model->customer_id]),
							'success' => new \yii\web\JsExpression('function(html){
										if(html=="success")
                                                    {
														$("#success_package").css("display", "block");
           												$("#success_package").html("Package details saved successfully.");
														setTimeout(function() {
														 $("#success_package").fadeOut();
														}, 3000 );


                                                    }else{
														$( ".help-block" ).remove();
                                                        $.each(html, function(key, val) {
                                                            $("#"+key).after("<div class=\"help-block\">"+val+"</div>");
                                                            $("#"+key).closest(".form-group").addClass("has-error");
                                                        });
                                                    }
								}'),
						],
						'options' => ['class' => 'btn btn-primary', 'type' => 'submit'],
						]);
						AjaxSubmitButton::end();
					?>	
					<?php echo Html::a('Cancel', Yii::$app->request->referrer, ['class' => 'btn btn-warning']) ?>
				</div>
				
				<?php ActiveForm::end(); ?>
              </div>
              <!-- /.tab-pane -->
              <div class="tab-pane <?php echo $strActive3; ?>" id="tab_3">
              <div class="row">
					<div class="col-md-12">
						<div style="display: none;" class="alert alert-success" id="success_bank"></div>
					</div>	
				</div>
               <?php 
				$form = ActiveForm::begin([
					
				]);
				 echo Html::hiddenInput('tab', 'bank'); 
				?>
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
							 <?php echo $form->field($modelLinkCustPackage, 'bank_type')->radioList(array('virtual'=>'Bank Virtual ACC','solnet'=>'SOLNET Bank Account'),['itemOptions' => ['class' =>'bank-type-radio']]); ?>
					</div>
			  </div>
              <div class="row virtual">
              	<div class="col-md-12 form-group required text-center">
              		<div class="col-md-3"></div>
              		<div class="col-md-6 text-center">
              			<?php echo $form->field($modelLinkCustPackage, 'bank_name')->textInput(); ?>
              			<?php echo $form->field($modelLinkCustPackage, 'virtual_acc_no')->textInput(); ?>
              			<?php echo $form->field($modelLinkCustPackage, 'account_name')->textInput(); ?>
              		</div>
              		<div class="col-md-3"></div>
              	</div>
              </div>
              <div class="row solnet">
              	<div class="col-md-12 form-group required text-center">
              		<div class="col-md-3"></div>
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
              			<?php echo $form->field($modelLinkCustPackage, 'bankname')->textInput(['readOnly'=>true]); ?>
              			<?php echo $form->field($modelLinkCustPackage, 'accname')->textInput(['readOnly'=>true]); ?>
              			<?php echo $form->field($modelLinkCustPackage, 'bankcurrency')->textInput(['readOnly'=>true]); ?>
              			<?php echo $form->field($modelLinkCustPackage, 'bankbranch')->textInput(['readOnly'=>true]); ?>
              		</div>
              		<div class="col-md-3"></div>
              	</div>
              </div>
              <div class="form-group">
					<?php AjaxSubmitButton::begin([
						'label' => 'Save Bank Details',
						'ajaxOptions' => [
							'type'=>'POST',
							'url'=>Url::to(['customer/plan','id'=>$model->customer_id]),
							'success' => new \yii\web\JsExpression('function(html){
									if(html=="success")
                                                    {
														$("#success_bank").css("display", "block");
           												$("#success_bank").html("Bank details saved successfully.");
														setTimeout(function() {
														 $("#success_bank").fadeOut();
														}, 3000 );


                                                    }else{
														$( ".help-block" ).remove();
                                                        $.each(html, function(key, val) {
                                                            $("#"+key).after("<div class=\"help-block\">"+val+"</div>");
                                                            $("#"+key).closest(".form-group").addClass("has-error");
                                                        });
                                                    }
								}'),
						],
						'options' => ['class' => 'btn btn-primary', 'type' => 'submit'],
						]);
						AjaxSubmitButton::end();
					?>	
					<?php echo Html::a('Cancel', Yii::$app->request->referrer, ['class' => 'btn btn-warning']) ?>
					<?php //echo Html::submitButton('Submit', ['class'=> 'btn btn-primary']) ;?>	
				</div>
             <?php ActiveForm::end(); ?>
              </div>
              <!-- /.tab-pane -->
            </div>
            <!-- /.tab-content -->
          </div>
          <!-- nav-tabs-custom -->
        </div>
        <!-- /.col -->

       
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
		$('#linkcustomepackage-bank_type input[type=radio]').change(function(){
			
    		if($(this).val()=='virtual')  {
				$('.solnet').hide();
				$('.virtual').show();
			}else{
				$('.solnet').show();
				$('.virtual').hide();
			}
      
      });
		
	 /* var intPaymentType = '<?php echo $modelLinkCustPackage->payment_type; ?>';
		
		if(intPaymentType=='term'){
			  $('.term').show();
			  $('.bulk').hide();
			  $('.picker').hide();
			  $('.pay_in_advanc_grace_period').hide();
			  $('.bulk_grace_period').hide();
			  $('.term_grace_period').show();
		}else if(intPaymentType=='bulk'){
			  $('.picker').show();
			  $('.bulk').show();
			  $('.term').hide();
			  $('.pay_in_advanc_grace_period').hide();
			  $('.bulk_grace_period').show();
			  $('.term_grace_period').hide();
		}else{
			  $('.term').hide();
			  $('.bulk').hide();
	  		  $('.picker').hide();		
	  		  $('.pay_in_advanc_grace_period').show();
			  $('.bulk_grace_period').hide();
			  $('.term_grace_period').hide();	
		}*/
		
		var intPaymentType = '<?php echo $modelLinkCustPackage->payment_type; ?>';
		
		if(intPaymentType=='term'){
			  $('.term').show();
			  $('.pay_in_advanc_grace_period').hide();
			  $('.bulk_grace_period').hide();
			  $('.term_grace_period').show();
			  $('.picker').hide();
			  $('.bulk').hide();
		  }
		  else if(intPaymentType=='bulk')
		  {
			  $('.picker').show();
			  $('.pay_in_advanc_grace_period').hide();
			  $('.bulk_grace_period').show();
			  $('.term_grace_period').hide();
			  $('.term').hide();
			  $('.bulk').show();
		  }
		  else{
			  $('.picker').hide();
			  $('.pay_in_advanc_grace_period').show();
			  $('.bulk_grace_period').hide();
			  $('.term_grace_period').hide();
			  $('.term').hide();
			  $('.bulk').hide();
			  grace_period_type = 'PAY_IN_ADVANC';
			  grace_period_id= 'linkcustomepackage-pay_in_advanc_grace_period';
			   $.ajax({
				  url: '<?php echo yii::$app->request->baseUrl;?>/customer/getgraceperiodtype',
				  type: "GET",
					  data: {id : grace_period_type},
				  dataType:'json',
				  cache: false,
				  success: function(response){
					$("#"+grace_period_id).val(response);
				  }
				});
		  }
		  
	  
	  $('#linkcustomepackage-payment_type').change(function(){
		 
		  var grace_period_type= '';
		  var grace_period_id= '';

		  if($(this).val()=='term'){
		  	  $('.pay_in_advanc_grace_period').hide();
		  	  $('.bulk_grace_period').hide();
		  	  $('.term_grace_period').show();
			  $('.term').show();
			  $('.picker').hide();
			  grace_period_type = 'TERM_PAYMENT';
			  grace_period_id= 'linkcustomepackage-term_grace_period';
		  }
		  else if($(this).val()=='bulk')
		  {
			  $('.picker').show();
			  $('.term').hide();
			  $('.pay_in_advanc_grace_period').hide();
			  $('.bulk_grace_period').show();
			  $('.term_grace_period').hide();
			  grace_period_type = 'BULK_PAYMENT';
			  grace_period_id= 'linkcustomepackage-bulk_grace_period';
		  }
		  else{
			  $('.picker').hide();
			  $('.term').hide();
			  $('.pay_in_advanc_grace_period').show();
			  $('.bulk_grace_period').hide();
			  $('.term_grace_period').hide();	
			  grace_period_type = 'PAY_IN_ADVANC';
			  grace_period_id= 'linkcustomepackage-pay_in_advanc_grace_period';
		  }
		   $.ajax({
			  url: '<?php echo yii::$app->request->baseUrl;?>/customer/getgraceperiodtype',
			  type: "GET",
				  data: {id : grace_period_type},
			  dataType:'json',
			  cache: false,
			  success: function(response){
				$("#"+grace_period_id).val(response);
			  }
			});
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
		
		/***********************/
			/*$('form')
			.each(function() {
				$(this).data('serialized', $(this).serialize())
			})
			.on('change input', 'input, select, textarea', function(e) {
				var $form = $(this).closest("form");
				var state = $form.serialize() === $form.data('serialized');    
				$form.find('input:submit, button:submit').prop('disabled', state);
			})
			.find('input:submit, button:submit')
			.prop('disabled', true); */
		/***********************/
		var strbankId = '<?php echo $modelLinkCustPackage->bank_id; ?>';
			if(strbankId!=""){
			$.ajax({
						  url: '<?php echo yii::$app->request->baseUrl;  ?>/customer/getbankdetails',
						  type: "GET",
						  data: {id : '<?php echo $modelLinkCustPackage->bank_id; ?>'},
						  dataType:'json',
						  cache: false,
						  success: function(response){
								var processData = response;
								$("#linkcustomepackage-bankname").val(processData[0]);
								$("#linkcustomepackage-accname").val(processData[1]);
								$("#linkcustomepackage-bankcurrency").val(processData[3]);
								$("#linkcustomepackage-bankbranch").val(processData[2]);
						  }
						});
		}
		
		
        $('#btn-example-file-reset').on('click', function(e){
           var $el = $('#uploadFile');
           $el.wrap('<form>').closest('form').get(0).reset();
           $el.unwrap();
        });
		
	/* 	ClassicEditor
			.create( document.querySelector( '#editor' ) )
			.then( editor => {
			console.log( editor );
		} )
		
		.catch( error => {
			console.error( error );
		} );*/
		  
		
		CKEDITOR.replace('editor');
		for (var i in CKEDITOR.instances) {
                
                CKEDITOR.instances[i].on('change', function() { CKEDITOR.instances[i].updateElement() });
                
        } 
   
	});
</script>