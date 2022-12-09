<?php
/*
############################################################################################
# eLuminous Technologies - Copyright (@) http://eluminoustechnologies.com
# This code is written by eLuminous Technologies, Its a sole property of
# eLuminous Technologies and cant be used / modified without license.
# Any changes/ alterations, illegal uses, unlawful distribution, copying is strictly
# prohibhited
############################################################################################
# Name : reminder_model.php
# Created on : 14th July 2017 by Swati Jadhav.
# Update on  : 14th July 2017 by Swati Jadhav.
# Purpose : Send reminder mail those whoes due date after 7 days.
############################################################################################
*/
use yii\helpers\Html;
use dosamigos\datepicker\DatePicker;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
?>
<div class="box box-primary">
	<div class="box-body">
		
			<?php $form = ActiveForm::begin(['id'=>'send_reminder_form',
				'action' =>['invoice/sendreminder', 'id' =>$id],'enableAjaxValidation' => true])?>
			 <div class="reminder-form">
                 
                    <div class="col-md-12" >
                    <?php 
                  
                    if($model->customer->user_type == 'corporate'){ ?>

                        <div class="col-md-6 form-group">
                        <?php echo $form->field($model, 'email_it')->checkbox(['label'=>'IT Incharge','value'=>$model->customer->email_it]);?>

                       <?php echo $form->field($model, 'email_finance')->checkbox(['label'=>'Finance Department','value'=>$model->customer->email_finance]); ?>

                        <?php echo  $form->field($model, 'other_email')->textInput(['placeholder'=>'[Optional]'])->hint('(Add multiple Email using comma)')->label('Add More Email') ?>

                       </div>
                  <?php }
                    else{?>
                    <?php echo $form->field($model, 'email_address')->textInput(['value'=> $model->customer->email_address,'readonly' => 'true']) ?>


                    <?php echo $form->field($model, 'other_email')->textInput(['placeholder'=>'[Optional]'])->hint('(Add multiple Email using comma)')->label('Add More Email') ?>

                   <?php }?>
                  </div>
         
           
			<div class="form-group" >
			<?php echo Html::submitButton('Submit', ['class'=> 'btn btn-primary']) ;?>	
			<?php echo Html::button('Quit', ['class'=> 'btn btn-default closemodal']) ;?>
			</div>
		</div>
			<?php ActiveForm::end(); ?>
	
   </div>
</div>

<script type="text/javascript">
	$('.closemodal').click(function() {
    $('#modal').modal('hide');
});
</script>