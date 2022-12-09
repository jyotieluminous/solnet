<?php
use yii\helpers\Html;
use dosamigos\datepicker\DatePicker;
use yii\widgets\ActiveForm;
use yii\helpers\Url;


?>
<div class="box box-default">
	<div class="box-body">
		<div class="tbllanguage-form">
			<?php $form = ActiveForm::begin(['id'=>'comment_form','action' =>['customerpackage/updatestatus', 'id' =>$id]])?>
			 <div class="prospect-form">
	            <div class="row">
		            <div class="col-md-12">
		               	<table class="table table-hover table-responsive EquipmentsTable">
		                    <thead>
		                        <th>#</th>
		                        <th>Quntity</th>
		                        <th>Return Quntity</th>
		                        <th>Broken Quntity</th>
		                        <th>Remark</th>
		                    </thead>
		                    <tbody>
		                    	<?php $intCnt = 1;
		                    	foreach ($arrResultEqupment as $key => $arrValue) { 
    								//echo '<pre>';print_r($arrValue->quantity);echo '</pre>';die; ?>
                                <tr>
                                    <td><?php echo $intCnt++; ?></td>
                                    <td><?php echo $arrValue->quantity; ?></td>
                                    <?php echo   $form->field($arrValue, 'quantity')->hiddenInput()->label(false); ?>

                                    <td><?php  echo $form->field($arrValue, 'return_equipment_quntity')->textInput()->label(false) ?></td>
                                    
                                    <td><?php  echo $form->field($arrValue, 'broken_equipment_quntity')->textInput()->label(false) ?></td>
                                    <td>
                                        <?php  echo $form->field($arrValue, 'remark')->textarea()->label(false) ?>
                                    </td>
                                </tr>
                            <?php } ?>
		                    </tbody>
		                </table>
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
    $('#modal').modal('hide');
});

	
</script>
