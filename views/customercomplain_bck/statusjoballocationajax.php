<?php
use yii\helpers\Html;
use dosamigos\datepicker\DatePicker;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use dosamigos\datetimepicker\DateTimePicker;
use kartik\select2\Select2;

if($complain_type == 'Broadband') {
	$link = 'customercomplain/nocsubmitbrespond';
} else if($complain_type == 'Dedicated'){
	$link = 'customercomplain/nocsubmitdrespond';
} else if($complain_type == 'Local Loop'){
	$link = 'customercomplain/nocsubmitlrespond';
}

//echo '<pre>';print_r($model);die;
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
						<?php echo $form->field($model, 'ticket_status')->dropDownList([''=>'Select Status Status','open' => 'Open', 'closed' => 'Closed']); ?>
						</div>						
					</div>       					
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
