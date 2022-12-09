<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use dosamigos\datepicker\DatePicker;
?>
<div class="box box-default">
	<div class="box-body">
		<div class="tbllanguage-form">
			<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
				<div class="row">
					<div class="col-md-12">
						<div class="form-group required col-md-6">
						<?php echo $form->field($model, 'fk_user_id')->dropDownList($arrSales,['prompt'=>'Select Sales Person']); ?>
						</div>
					</div>
					<div class="col-md-12">
						<div class="form-group required col-md-6">
							<?php echo $form->field($model, 'agent_name')->textInput(['maxlength' => true]) ?>
						</div>
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

	  	var pay_type = $('#linkcustomepackage-payment_type').val();	
		if(pay_type=='term'){
			$('.term').show();
			$('.picker').hide();
			$('.bulk').hide();
		}else if(pay_type=='bulk'){
			$('.picker').show();
			$('.term').hide();
			$('.bulk').show();
		}else{
			$('.picker').hide();
			$('.term').hide();
			$('.bulk').hide();
		}

		/*$('.term').hide();
		  $('.bulk').hide();
		  $('.picker').hide();*/

	  	$('#linkcustomepackage-payment_type').change(function(){
	  		if($(this).val()=='term'){
			  $('.term').show();
			  $('.picker').hide();
			  $('.bulk').hide();
		  	}
		  	else if($(this).val()=='bulk')
		  	{
			  $('.picker').show();
			  $('.term').hide();
			  $('.bulk').show();
			}
		  	else{
			  $('.picker').hide();
			  $('.term').hide();
			  $('.bulk').hide();
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

	$('#btn-example-file-reset').on('click', function(e){
       var $el = $('#uploadFile');
       $el.wrap('<form>').closest('form').get(0).reset();
       $el.unwrap();
    });
});
</script>