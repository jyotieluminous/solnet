<?php
use yii\helpers\Html;
use dosamigos\datepicker\DatePicker;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use dosamigos\datetimepicker\DateTimePicker;
if($complain_type == 'Broadband') {
	$link = 'customercomplain/submitbrespond';
} else if($complain_type == 'Dedicated'){
	$link = 'customercomplain/submitdrespond';
} else if($complain_type == 'Local Loop'){
	$link = 'customercomplain/submitlrespond';
}
?>
<div class="box box-default">
	<div class="box-body">
		<div class="tbllanguage-form">
			<?php $form = ActiveForm::begin(['id'=>'respond_form',
				'action' =>
				[$link, 'id' =>$id]

			])?>
			 <div class="prospect-form">
                 <div class="row" >
                 	<div class="col-md-12">
						<div class="form-group required col-md-12">						
						<?php echo $form->field($model, 'actual_problem')->textArea(['maxlength' => true]) ?>
						</div>						
					</div>
					<div class="col-md-12">
						<div class="form-group required col-md-12">						
						<?php echo $form->field($model, 'permanent_solution')->textArea(['maxlength' => true]) ?>
						</div>						
					</div>
					<div class="col-md-12">
						<div class="form-group required col-md-12">						
						<?php echo  $form->field($model, 'support_date_time')->widget(
								DateTimePicker::className(), [
									'clientOptions' => [
										'autoclose' => true,
										'size' => 'ms',
										'template' => '{input}',
										'format' => 'yyyy-mm-dd h:i',
										'inline' => true,
										'pickerPosition'=> "bottom-left",
									]
							]);
							?>
						</div>						
					</div>
                    <div class="col-md-12">
						<div class="form-group required col-md-12">						
						<?php echo $form->field($model, 'link_status')->dropDownList([''=>'Select Link Status','up' => 'UP', 'down' => 'Down', 'unstable' => 'UNSTABLE']); ?>
						</div>						
					</div>
					<?php
						$roleId = Yii::$app->user->identity->fk_role_id;
						## ONLY NOC ADMIN CAN CHANGE TICKET STATUS 
						//if($roleId=='26')
						//{
					?>
					<div class="col-md-12">
						<div class="form-group required col-md-12">						
						<?php echo $form->field($model, 'ticket_status')->dropDownList([''=>'Select Status Status','open' => 'Open', 'closed' => 'Closed']); ?>
						</div>						
					</div>
					<?php //} ?>       					
                  </div>
              </div>

			<div class="form-group" align="center">
			<?php echo Html::submitButton('Submit', ['class'=> 'btn btn-primary']) ;?>
			<?php echo Html::button('Quit', ['class'=> 'btn btn-default closemodal']) ;?>
			</div>
		</div>
			<?php ActiveForm::end(); ?>
	 </div>
   </div>
</div>

<script type="text/javascript">
	$('.closemodal').click(function() {
    	$(this).parent().parent().find('.modal-dialog').modal('hide');    
	});

	$(document).ready(function () {
        var today = new Date();
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            autoclose:true,
            endDate: "today",
            maxDate: today,
            defaultDate : today,
        }).on('changeDate', function (ev) {
                $(this).datepicker('hide');
            });


    });
function isNumber(evt) {
    var iKeyCode = (evt.which) ? evt.which : evt.keyCode
    if (iKeyCode != 46 && iKeyCode > 31 && (iKeyCode < 48 || iKeyCode > 57)){
        return false;
    }else{
      return true;
    }
  } 
  $( ".equmPrice" ).keyup(function() {

    var price    = $(this).parent().parent().find('.equmPrice').val();
    var quntity  = $(this).parent().parent().find('.equmQuntity').val();
    var intCnt   = $(this).parent().parent().find('.intCnt').val();
        
    var intTotalPrice = quntity * price;
    
    $(this).parents().find('.equmTotal_'+intCnt).val(intTotalPrice);
  });
</script>
