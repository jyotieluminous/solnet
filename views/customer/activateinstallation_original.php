<?php
use yii\helpers\Html;
use dosamigos\datepicker\DatePicker;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
?>
<div class="box box-default">
	<div class="box-body">
		<div class="tbllanguage-form">
			<?php $form = ActiveForm::begin(['id'=>'pending_form','action' =>
				['customer/submitactivation', 'id' =>$id]

			])?>
				<div class="row">
					
						<div class="col-md-12 form-group">
						<table align="center">
						<tr>
							 <td>
								<label>Customer Name :</label>
							 </td>
							 <td>
								<?php echo $arrCustomer->name; ?>
							</td>
						</tr>
						<tr>
							 <td>
								<label>Customer Address :</label>
							 </td>
							 <td>
							 <?php echo $arrCustomer->billing_address; ?>
							 </td>
						</tr>
						<tr>
							<td>
								<label>Package Title :</label>
							</td>
							<td>
								<?php echo $model->package->package_title; ?>
							</td>
						</tr>
						<tr>
							<td>
								<label>Package Speed :</label>
							</td>
							<td>
								<?php echo $model->package_speed; echo
								 $model->speed->speed_type; ?>
					 		</td>
					 	</tr>
					 	<tr>
					 		<td>
								<label>Installation Date:</label>
							</td>
					 		<td>
					 		<?php $strActivationDate = strtotime($model->activation_date);
								  if($model->activation_date!='0000-00-00 00:00:00')
								  {
								   $model->activation_date = date('Y-m-d',$strActivationDate);
								  }else{
								   $model->activation_date = '';
								  }?>
					 		<?php echo $form->field($model, 'activation_date')->widget(
								DatePicker::className(), [

									'clientOptions' => [
										'autoclose' => true,
										'format' => 'yyyy-mm-dd',
										//'startDate'=>date('Y-m-d')
									]
							])->label(false);?>
					 		</td>
					 	</tr>
					 	</table>
						
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