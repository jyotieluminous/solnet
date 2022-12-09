<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use dosamigos\datepicker\DatePicker;
use kartik\select2\Select2;
$this->title = 'Generate Custom Invoice';

?>
	<div class="box box-default">
		<div class="box-body">
			<div class="tbllanguage-form">
			<?php echo '&nbsp;'.Html::a('Show Custom Invoice', ['/invoice/index','CustomerinvoiceSearch[invoice_type]'=>'custom'], ['class' => 'btn btn-primary']) ?>
				<?php 
				$form = ActiveForm::begin();
				echo Html::hiddenInput('customer_id', '' ,['id'=>'customer_id']);
				?>
				 <div class="row">
				 	<div class="col-md-12">
					 <div class="col-md-6 text-right">
					 	<label>Send Invoice VIA Mail : </label>
					 	<?= $form->field($invoiceModel, 'send_invoice')->radioList(array('yes'=>'Yes','no'=>'No'))->label(false); ?>
					 </div>
					<div class="col-md-6 text-right">
						<label>Add Letter Head To Invoice PDF : </label>
						<input type="checkbox" value="" id="CustomerInvoices_print_header" onClick='headerChange()'>
					</div>
					<div class="col-md-6 text-right">
						<label>Add Signature To Invoice PDF : </label>
						<input type="checkbox" value="" id="CustomerInvoices_signature" onClick='signChange()'>
					</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="col-md-4"></div>
						<div class="form-group required col-md-4">
						<label>Solnet Customer ID</label><br/>
							<?php 
							if(isset($_GET['id']) && !empty($_GET['id']))
							{
								$intCustID = $_GET['id'];
							}
							else
							{
								$intCustID = '';
							}
							
								echo Select2::widget([
										'name' => 'solnet_customer_id',
										'data' => $data,
										'value'=> $intCustID,
										'options' => [
										'placeholder' => 'Select a Solnet Customer ID',
											'multiple' => false
										],
										'pluginOptions' => [
      										'allowClear' => true
								    	],
									'pluginEvents' => [
      
											"select2:selecting" => "function() {}",
											"select2:select" => "function() { 
																				var customer_id = $('#w1').val();
																				window.location.href = '".yii::$app->request->baseUrl."/invoice/generate/'+customer_id; 
											}",

										]
									]);
							?>

						</div>
						<div class="col-md-4"></div>
						<?php if(isset($_GET['id']) && !empty($_GET['id'])) { ?>
						
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="col-md-6 form-group required">
							<label>Customer Name</label><br/>
							<?php echo $model->name; ?>
						</div>
						<div class="col-md-6 form-group required">
							<label>Address</label><br/>
							<?php echo $model->billing_address; ?>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="col-md-6 form-group required">
							<label>Package Title</label><br/>
							<?php echo $model->linkcustomerpackage->package->package_title; ?>
						</div>
						<div class="col-md-6 form-group required">
							<label>Package Price</label><br/>
							<?php echo $model->linkcustomerpackage->currency->currency." ".number_format($model->linkcustomerpackage->package_price,2); ?>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="col-md-6 form-group required">
							<label>Invoice Send VIA</label><br/>
							<?php echo $model->invoice_send_via; ?>
						</div>						
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="col-md-6 form-group required">
							<?php echo $form->field($invoiceModel, 'usage_period_from')->widget(
								DatePicker::className(), [
									'clientOptions' => [
										'autoclose' => true,
										'format' => 'dd-mm-yyyy',

										//'startDate'=>date('Y-m-d')
									]
							]);?>
						</div>
						<div class="col-md-6 form-group required">
							<?php echo $form->field($invoiceModel, 'usage_period_to')->widget(
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
							<div class="col-md-6 form-group required">
								<?php echo $form->field($invoiceModel, 'current_invoice_amount')->textInput(); ?>
							</div>
							<div class="col-md-6 form-group required">
									<?php echo $form->field($invoiceModel, 'due_date')->widget(
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
							<?php echo $form->field($invoiceModel, 'installation_fee')->textInput(); ?>
						</div>
						<div class="col-md-6 form-group">
							<?php echo $form->field($invoiceModel, 'other_service_fee')->textInput();  ?>
						</div>
						
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="col-md-6">
							<?php echo $form->field($invoiceModel, 'comments')->textArea(); ?>
						</div>
					</div>
				</div>
					
				<div class="form-group">
					<?php echo Html::submitButton('Submit', ['class'=> 'btn btn-primary', 'id' => 'btn_gnt_invoice']) ;?>	
					<?php echo Html::a('Cancel', Yii::$app->request->referrer, ['class' => 'btn btn-warning']) ?>
				</div>
				<?php 
					}
				?>
				<?php ActiveForm::end(); ?>
			</div>
		</div>
	</div>
<script type="text/javascript">
	function headerChange() {
 var isHeader = document.getElementById('CustomerInvoices_print_header').checked;
  var basePath = "<?php echo Yii::$app->request->baseUrl;?>/";
  var action = "invoice/setsession";
  var getURl = basePath + action;
  var headerValue = isHeader;
  
  $.ajax({
   type: 'POST',
   url: getURl,
   data: { "header_flag" : headerValue }, 
 
  });
}

function signChange() {
	
 var isHeader = document.getElementById('CustomerInvoices_signature').checked;
  var basePath = "<?php echo Yii::$app->request->baseUrl;?>/";
  var action = "invoice/setsignature";
  var getURl = basePath + action;
  var headerValue = isHeader;

  $.ajax({
   type: 'POST',
   url: getURl,
   data: { "header_flag" : headerValue }, 
 
  });
}

</script>
<script type="text/javascript">
	$(document).ready(function() 
	{
		$('#corporate').hide();
		$('#customer-user_type input[type=radio]').change(function(){
    		if($(this).val()=='home')  {
				$('#corporate').hide();
				$('#home').show();
			}else{
				$('#corporate').show();
				$('#home').hide();
			}
      
      });
		
	  $('.term').hide();
	  $('.picker').hide();
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
	  
	  $('#customer-is_address_same').click(function()
  	  {
		  if ($('#customer-is_address_same').is(":checked")){
			 var strBillingAddr =  $('#customer-billing_address').val();
			  $('#linkcustomepackage-installation_address').val(strBillingAddr);
		  }else{
			  $('#linkcustomepackage-installation_address').val('');
		  }
	  });
	  
		
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
   
	});

	$('form').on('beforeSubmit',function() {
	    $('#btn_gnt_invoice').attr('disabled','disabled');
	    return true;
	});
</script>