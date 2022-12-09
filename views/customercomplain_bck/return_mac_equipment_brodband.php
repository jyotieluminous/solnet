<?php
use yii\helpers\Html;
use dosamigos\datepicker\DatePicker;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use dosamigos\datetimepicker\DateTimePicker;
use kartik\select2\Select2;

$link = 'customercomplain/returnmacequipmentbroadband/';

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
                 		<input type="hidden" name="equipment_type" value="<?php echo $type ?>">
                 		<input type="hidden" name="equipment_id" value="<?php echo $model->equipments_mac_id ?>">
                 		<input type="hidden" name="fk_complain_id" value="<?php echo $model->fk_comp_id; ?>">
						<div class="form-group required col-md-12">
							<label>Status</label>
							<select class="form-control" name="status" id="status" required="">
								<option value="">Please select a status</option>
								<option value="active" <?php if($model->status == 'active'){ echo 'selected';} ?> >active</option>
								<option value="returned" <?php if($model->status == 'returned'){ echo 'selected';} ?> > Returned</option>
								<option value="broken" <?php if($model->status == 'broken'){ echo 'selected';} ?> >Broken</option>
							</select>
						</div>	
					</div>
					<div class="col-md-12">
                 		<div class="form-group required col-md-12">
							<label>Return reasone</label>
							<textarea class="form-control" name="return_mac_reasone"><?php echo $model->return_mac_reasone ?></textarea>
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
</div>

<script type="text/javascript">
	$('.closemodal').click(function() {
    	$(this).parent().parent().find('.modal-dialog').modal('hide');    
	});
</script>
