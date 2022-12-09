<?php
use yii\helpers\Html;
use dosamigos\datepicker\DatePicker;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
?>
<div class="box box-default">
	<div class="box-body">
		<div class="tbllanguage-form">
			<?php $form = ActiveForm::begin(['id'=>'comment_form',
				'action' =>
				['customerpackage/updatecomment', 'id' =>$id]

			])?>
			 <div class="prospect-form">
            <div class="row">
              
            <div class="col-md-12">
               <div class="col-md-2"></div>
              <div class="col-md-8 form-group" >
               <?php echo $form->field($model, 'reason_for_disconnection')->textArea() ?>
              </div>
              <div class="col-md-2"></div>
            </div>

            </center>
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
    $('#modal').modal('hide');
});

	
</script>
